<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\{Company,
    Customer,
    LoyaltyPoint,
    Payment,
    PaymentMethod,
    Stock,
    Transaction,
    TransactionItem,
    UnknownItem};
use App\Services\RegisterSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        } catch (Exception $e) {
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

        } catch (Exception $e) {
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

        $sessionData = RegisterSessionService::exportSessionData();
        if (empty($sessionData['cart'])) {
            return back()->with('error', 'Le panier est vide.');
        }

        // Vérifier si le montant payé correspond au total
        $arrondissementEnabled = config('custom.register.arrondissementMethod');
        $totalToCheck = $sessionData['totals']['total'];
        if ($arrondissementEnabled) {
            $totalToCheck = round($totalToCheck * 20) / 20;
        }
        $totalPaid = collect($request->payments)->sum('amount');
        if ($totalPaid < $totalToCheck) {
            return back()->with('error', 'Le montant payé est insuffisant.');
        }

        $customer = $sessionData['customer'] ?? null;
        $company = $sessionData['company'] ?? null;

        try {
            DB::beginTransaction();

            // Calculer les totaux depuis les items du panier
            $subtotalHT = 0;
            $subtotalTTC = 0;
            $totalTaxAmount = 0;
            $totalDiscountAmount = 0;
            $totalCost = 0;
            $totalMargin = 0;

            foreach ($sessionData['cart'] as $item) {
                // Correction : le prix de vente est TTC dans la caisse
                $unitPriceTTC = $item['unit_price'] ?? 0; // Prix de vente TVAC
                $taxRate = $item['tax_rate'] ?? 0;
                $unitPriceHT = $taxRate > 0 ? $unitPriceTTC / (1 + $taxRate / 100) : $unitPriceTTC;

                // Calculer les totaux
                $quantity = $item['quantity'] ?? 1;
                $totalPriceHT = $unitPriceHT * $quantity;
                $totalPriceTTC = $unitPriceTTC * $quantity;
                $taxAmount = $totalPriceTTC - $totalPriceHT;

                // Calculer les remises
                $discountRate = $item['discount_rate'] ?? 0;
                $discountAmount = $item['discount_amount'] ?? 0;

                // Calculer le coût et la marge
                $costPrice = $item['cost_price'] ?? 0;
                $totalCost = $costPrice * $quantity;
                $margin = $totalPriceTTC - $discountAmount - $totalCost;

                $subtotalHT += $totalPriceHT;
                $subtotalTTC += $totalPriceTTC;
                $totalTaxAmount += $taxAmount;
                $totalDiscountAmount += $discountAmount;
            }

            // Préparer les données de la transaction
            $transactionData = [
                'cashier_id' => auth()->id(),
                'cash_register_id' => RegisterSessionService::getCurrentCashRegister(),
                'transaction_type' => 'ticket',
                'total_amount' => $sessionData['totals']['total'],
                'items_count' => $sessionData['totals']['items_count'],
                'payment_status' => 'paid',
                'notes' => $request->notes,
                'subtotal_ht' => $subtotalHT,
                'subtotal_ttc' => $subtotalTTC,
                'tax_amount' => $totalTaxAmount,
                'discount_amount' => $totalDiscountAmount,
                'total_cost' => 0,
                'total_margin' => 0,
                'margin_percentage' => 0,
                'currency' => 'EUR',
                'exchange_rate' => 1.000000,
                'status' => 'completed',
                'is_wix_release' => false,
                'discounts_data' => !empty($sessionData['discounts']) ? $sessionData['discounts'] : null,
            ];

            // Ajouter les champs client seulement si activé
            if (config('app.register_customer_management')) {
                $transactionData['customer_id'] = (isset($customer) && isset($customer['type']) && $customer['type'] === 'customer') ? ($customer['id'] ?? null) : null;
                $transactionData['company_id'] = (isset($customer) && isset($customer['type']) && $customer['type'] === 'company') ? ($customer['id'] ?? null) : null;
            }

            $transaction = Transaction::create($transactionData);

            // 2. Ajouter les items
            foreach ($sessionData['cart'] as $item) {
                // Correction : le prix de vente est TTC dans la caisse
                $unitPriceTTC = $item['unit_price'] ?? 0; // Prix de vente TVAC
                $taxRate = $item['tax_rate'] ?? 0;
                $unitPriceHT = $taxRate > 0 ? $unitPriceTTC / (1 + $taxRate / 100) : $unitPriceTTC;

                // Calculer les totaux
                $quantity = $item['quantity'] ?? 1;
                $totalPriceHT = $unitPriceHT * $quantity;
                $totalPriceTTC = $unitPriceTTC * $quantity;
                $taxAmount = $totalPriceTTC - $totalPriceHT;

                // Calculer les remises
                $discountRate = $item['discount_rate'] ?? 0;
                $discountAmount = $item['discount_amount'] ?? 0;

                // Calculer le coût et la marge
                $costPrice = $item['cost_price'] ?? 0;
                $totalCost = $costPrice * $quantity;
                $margin = $totalPriceTTC - $discountAmount - $totalCost;

                // Déterminer la source
                $isArticleInconnu = empty($item['variant_id']) || empty($item['stock_id']);
                $source = $isArticleInconnu ? 'unknown_item' : 'stock';

                $transactionItem = $transaction->items()->create([
                    'variant_id' => $item['variant_id'],
                    'stock_id' => $item['stock_id'],
                    'article_name' => $item['article_name'],
                    'variant_reference' => $item['variant_reference'] ?? null,
                    'variant_attributes' => $item['variant_attributes'] ?? null,
                    'barcode' => $item['barcode'] ?? null,
                    'quantity' => $quantity,
                    'unit_price_ht' => $unitPriceHT,
                    'unit_price_ttc' => $unitPriceTTC,
                    'total_price_ht' => $totalPriceHT,
                    'total_price_ttc' => $totalPriceTTC,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'discount_rate' => $discountRate,
                    'discount_amount' => $discountAmount,
                    'total_cost' => $totalCost,
                    'margin' => $margin,
                    'source' => $source,
                ]);

                // Si c'est un article inconnu, créer l'enregistrement dans unknown_items
                if ($isArticleInconnu) {
                    UnknownItem::create([
                        'transaction_item_id' => $transactionItem->id,
                        'nom' => $item['article_name'] ?? 'Article inconnu',
                        'description' => $item['description'] ?? null,
                        'prix' => $unitPriceTTC,
                        'tva' => $taxRate,
                        'note_interne' => null,
                        'est_regularise' => false,
                    ]);
                }

                // 3. Décompter le stock en FIFO uniquement si ce n'est pas un article inconnu
                if (!$isArticleInconnu) {
                    $this->decrementStockFIFO($transactionItem, $item['variant_id'], $quantity, $item['article_name'] ?? null);
                }
            }

            // 3. Créer les paiements
            foreach ($request->payments as $paymentData) {
                $transaction->payments()->create([
                    'payment_method_id' => $paymentData['payment_method_id'],
                    'amount' => $paymentData['amount'],
                    'status' => 'completed',
                ]);
            }

            // 4. Mettre à jour les totaux de coût et marge après les mouvements de stock
            $totalCost = $transaction->items()
                ->with('stockMovements')
                ->get()
                ->sum(function ($item) {
                    return $item->stockMovements->sum('total_cost');
                });

            $totalMargin = $subtotalTTC - $totalDiscountAmount - $totalCost;
            $marginPercentage = $subtotalTTC > 0 ? ($totalMargin / $subtotalTTC) * 100 : 0;

            $transaction->update([
                'total_cost' => $totalCost,
                'total_margin' => $totalMargin,
                'margin_percentage' => $marginPercentage,
            ]);

            // Attribution des points de fidélité
            $loyaltyStep = config('custom.loyalty_point_step', 1);
            $totalPaid = $transaction->total_amount;
            Log::info('loyaltyStep: ' . $loyaltyStep);
            Log::info('totalPaid: ' . $totalPaid);
            $points = 0;
            if ($loyaltyStep > 0) {
                $points = floor($totalPaid / $loyaltyStep);
                Log::info('points: ' . $points);
            }
            if ($points > 0) {
                $loyaltyData = [
                    'transaction_id' => $transaction->id,
                    'points' => $points,
                    'type' => 'earned',
                    'description' => 'Points fidélité pour la transaction #' . $transaction->id,
                ];
                if ($transaction->customer_id) {
                    $loyaltyData['customer_id'] = $transaction->customer_id;
                }
                if ($transaction->company_id) {
                    $loyaltyData['company_id'] = $transaction->company_id;
                }
                LoyaltyPoint::create($loyaltyData);
                // Mise à jour du total de points sur le profil client/compagnie
                if ($transaction->customer_id) {
                    $customer = Customer::find($transaction->customer_id);
                    if ($customer) {
                        $nouveauTotal = ($customer->loyalty_points ?? 0) + $points;
                        $customer->loyalty_points = $nouveauTotal;
                        $customer->save();
                    }
                }
                if ($transaction->company_id) {
                    $company = Company::find($transaction->company_id);
                    if ($company) {
                        $nouveauTotal = ($company->loyalty_points ?? 0) + $points;
                        $company->loyalty_points = $nouveauTotal;
                        $company->save();
                    }
                }
            }

            // 5. Vider la session
            RegisterSessionService::clearSession();

            DB::commit();

            return redirect()->route('tickets.index', $transaction->id)
                             ->with('success', 'Vente finalisée avec succès.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la finalisation de la vente: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
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

    /**
     * Décompte le stock en FIFO (First In, First Out)
     */
    private function decrementStockFIFO(TransactionItem $transactionItem, $variantId, $quantity, $articleName = null)
    {
        $remainingQuantity = $quantity;

        // Récupérer tous les stocks disponibles pour ce variant, triés par date de création (plus ancien en premier)
        $availableStocks = Stock::where('variant_id', $variantId)
            ->where('quantity', '>', 0)
            ->orderBy('created_at', 'asc') // FIFO : plus ancien en premier
            ->get();

        foreach ($availableStocks as $stock) {
            if ($remainingQuantity <= 0) {
                break; // On a décompté toute la quantité nécessaire
            }

            // Calculer combien on peut prendre de ce stock
            $quantityToTake = min($remainingQuantity, $stock->quantity);

            // Créer le mouvement de stock
            $transactionItem->stockMovements()->create([
                'stock_id' => $stock->id,
                'quantity_used' => $quantityToTake,
                'cost_price' => $stock->buy_price,
                'total_cost' => $quantityToTake * $stock->buy_price,
                'lot_reference' => $stock->lot_reference,
            ]);

            // Décompter du stock
            $stock->decrement('quantity', $quantityToTake);

            // Mettre à jour la quantité restante à décompter
            $remainingQuantity -= $quantityToTake;
        }

        // Si on n'a pas pu décompter toute la quantité, c'est un problème
        if ($remainingQuantity > 0) {
            $nom = $articleName ?? $variantId ?? 'Inconnu';
            throw new Exception("Stock insuffisant pour l'article '{$nom}'. Manquant: {$remainingQuantity}");
        }
    }
}
