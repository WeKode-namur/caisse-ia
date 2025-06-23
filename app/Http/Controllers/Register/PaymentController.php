<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Models\{PaymentMethod, Transaction, Payment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Récupère les méthodes de paiement disponibles
     */
    public function getMethods()
    {
        $methods = PaymentMethod::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'payment_methods' => $methods->map(function($method) {
                return [
                    'id' => $method->id,
                    'name' => $method->name,
                    'code' => $method->code,
                    'icon' => $method->icon,
                    'requires_reference' => $method->requires_reference,
                    'allows_partial_payment' => $method->allows_partial_payment,
                    'processing_fee_percentage' => $method->processing_fee_percentage
                ];
            })
        ]);
    }

    /**
     * Traite un paiement
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:255',
            'authorization_code' => 'nullable|string|max:100'
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);
        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // Vérifier que la transaction n'est pas déjà complètement payée
        $alreadyPaid = $transaction->payments()->where('status', 'completed')->sum('amount');
        $remainingAmount = $transaction->total_amount - $alreadyPaid;

        if ($remainingAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cette transaction est déjà entièrement payée'
            ], 422);
        }

        if ($request->amount > $remainingAmount) {
            return response()->json([
                'success' => false,
                'message' => "Le montant ne peut pas dépasser {$remainingAmount}€"
            ], 422);
        }

        // Vérifier si une référence est requise
        if ($paymentMethod->requires_reference && !$request->reference) {
            return response()->json([
                'success' => false,
                'message' => 'Une référence est requise pour ce mode de paiement'
            ], 422);
        }

        try {
            // Créer le paiement
            $payment = Payment::create([
                'transaction_id' => $transaction->id,
                'payment_method_id' => $paymentMethod->id,
                'amount' => $request->amount,
                'currency' => 'EUR',
                'reference' => $request->reference,
                'authorization_code' => $request->authorization_code,
                'status' => 'pending'
            ]);

            // Calculer les frais de traitement
            $payment->calculateProcessingFee();

            // Simuler le traitement du paiement (à adapter selon vos besoins)
            $paymentResult = $this->processPaymentMethod($payment, $paymentMethod);

            if ($paymentResult['success']) {
                $payment->markAsCompleted(
                    $paymentResult['authorization_code'] ?? null,
                    $paymentResult['external_transaction_id'] ?? null
                );

                // Vérifier si la transaction est maintenant complètement payée
                $totalPaid = $transaction->payments()->where('status', 'completed')->sum('amount');
                if ($totalPaid >= $transaction->total_amount) {
                    $transaction->update(['payment_status' => 'paid']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Paiement traité avec succès',
                    'payment' => [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'status' => $payment->status,
                        'method' => $paymentMethod->name,
                        'reference' => $payment->reference,
                        'processing_fee' => $payment->processing_fee
                    ],
                    'transaction' => [
                        'id' => $transaction->id,
                        'payment_status' => $transaction->payment_status,
                        'total_paid' => $totalPaid,
                        'remaining_amount' => max(0, $transaction->total_amount - $totalPaid)
                    ]
                ]);
            } else {
                $payment->markAsFailed($paymentResult['error']);

                return response()->json([
                    'success' => false,
                    'message' => 'Échec du paiement: ' . $paymentResult['error']
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement du paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcule la monnaie à rendre
     */
    public function calculateChange(Request $request)
    {
        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'amount_given' => 'required|numeric|min:0'
        ]);

        $totalAmount = $request->total_amount;
        $amountGiven = $request->amount_given;
        $change = $amountGiven - $totalAmount;

        if ($change < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Le montant donné est insuffisant',
                'shortage' => abs($change)
            ], 422);
        }

        return response()->json([
            'success' => true,
            'change' => $change,
            'breakdown' => $this->calculateChangeBreakdown($change)
        ]);
    }

    /**
     * Traite un remboursement
     */
    public function processRefund(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'reason' => 'nullable|string|max:255'
        ]);

        $payment = Payment::findOrFail($request->payment_id);

        if (!$payment->can_be_refunded) {
            return response()->json([
                'success' => false,
                'message' => 'Ce paiement ne peut pas être remboursé'
            ], 422);
        }

        try {
            $payment->refund(auth()->id(), $request->reason);

            // Mettre à jour le statut de la transaction
            $transaction = $payment->transaction;
            $totalPaid = $transaction->payments()->where('status', 'completed')->sum('amount');

            if ($totalPaid < $transaction->total_amount) {
                $transaction->update(['payment_status' => 'pending']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paiement remboursé avec succès',
                'payment' => [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'refunded_at' => $payment->refunded_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du remboursement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finalise une vente complète (création transaction + paiements multiples)
     */
    public function finalizeSale(Request $request)
    {
        $request->validate([
            'payments' => 'required|array|min:1',
            'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $sessionData = \App\Services\RegisterSessionService::exportSessionData();
        if (empty($sessionData['cart'])) {
            return back()->with('error', 'Le panier est vide.');
        }

        // Vérifier si le montant payé correspond au total
        $totalPaid = collect($request->payments)->sum('amount');
        if ($totalPaid < $sessionData['totals']['total']) {
            return back()->with('error', 'Le montant payé est insuffisant.');
        }

        try {
            DB::beginTransaction();

            // 1. Créer la transaction
            $transaction = Transaction::create([
                'cashier_id' => auth()->id(),
                'cash_register_id' => \App\Services\RegisterSessionService::getCurrentCashRegister(),
                'customer_id' => $sessionData['customer']['id'] ?? null,
                'transaction_type' => 'ticket',
                'total_amount' => $sessionData['totals']['total'],
                'payment_status' => 'paid', // Payé directement
                'notes' => $request->notes,
            ]);

            // 2. Ajouter les items
            foreach ($sessionData['cart'] as $item) {
                $itemData = $item;
                unset($itemData['id'], $itemData['added_at']);
                $transaction->items()->create($itemData);
            }
            
            // 3. Créer les paiements
            foreach ($request->payments as $paymentData) {
                $transaction->payments()->create([
                    'payment_method_id' => $paymentData['payment_method_id'],
                    'amount' => $paymentData['amount'],
                    'status' => 'completed',
                ]);
            }

            // 4. Vider la session
            \App\Services\RegisterSessionService::clearSession();

            DB::commit();
            
            return redirect()->route('panel.tickets.show', $transaction)
                             ->with('success', 'Vente finalisée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la finalisation de la vente: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la finalisation de la vente.');
        }
    }

    // ===== MÉTHODES PRIVÉES =====

    /**
     * Traite le paiement selon la méthode
     */
    private function processPaymentMethod(Payment $payment, PaymentMethod $method)
    {
        switch ($method->code) {
            case 'cash':
                // Paiement en espèces - toujours accepté
                return [
                    'success' => true,
                    'authorization_code' => 'CASH-' . now()->format('YmdHis')
                ];

            case 'card':
                // Simulation de paiement par carte
                return $this->processCardPayment($payment);

            case 'check':
                // Paiement par chèque - validation de la référence
                if (!$payment->reference) {
                    return [
                        'success' => false,
                        'error' => 'Numéro de chèque requis'
                    ];
                }
                return [
                    'success' => true,
                    'authorization_code' => 'CHECK-' . $payment->reference
                ];

            case 'bank_transfer':
                return [
                    'success' => true,
                    'authorization_code' => 'TRANSFER-' . now()->format('YmdHis')
                ];

            case 'gift_card':
                // Traité séparément dans le CartController
                return [
                    'success' => true,
                    'authorization_code' => 'GC-' . $payment->reference
                ];

            default:
                return [
                    'success' => false,
                    'error' => 'Méthode de paiement non supportée'
                ];
        }
    }

    /**
     * Simule le traitement d'un paiement par carte
     */
    private function processCardPayment(Payment $payment)
    {
        // Simulation - Dans un vrai système, vous intégreriez avec un terminal de paiement

        // Simuler un délai de traitement
        usleep(500000); // 0.5 seconde

        // Simuler un taux de succès de 95%
        if (rand(1, 100) <= 95) {
            return [
                'success' => true,
                'authorization_code' => 'AUTH-' . strtoupper(uniqid()),
                'external_transaction_id' => 'TXN-' . now()->format('YmdHis') . '-' . rand(1000, 9999)
            ];
        } else {
            $errors = [
                'Carte refusée',
                'Fonds insuffisants',
                'Carte expirée',
                'Erreur de communication',
                'Transaction annulée par le porteur'
            ];

            return [
                'success' => false,
                'error' => $errors[array_rand($errors)]
            ];
        }
    }

    /**
     * Calcule la répartition de la monnaie
     */
    private function calculateChangeBreakdown($amount)
    {
        if ($amount <= 0) {
            return [];
        }

        $denominations = [
            500 => '500€',
            200 => '200€',
            100 => '100€',
            50 => '50€',
            20 => '20€',
            10 => '10€',
            5 => '5€',
            2 => '2€',
            1 => '1€',
            0.50 => '50c',
            0.20 => '20c',
            0.10 => '10c',
            0.05 => '5c',
            0.02 => '2c',
            0.01 => '1c'
        ];

        $breakdown = [];
        $remaining = round($amount, 2);

        foreach ($denominations as $value => $label) {
            if ($remaining >= $value) {
                $count = floor($remaining / $value);
                $breakdown[] = [
                    'denomination' => $label,
                    'count' => $count,
                    'value' => $value,
                    'total' => $count * $value
                ];
                $remaining = round($remaining - ($count * $value), 2);
            }
        }

        return $breakdown;
    }
}
