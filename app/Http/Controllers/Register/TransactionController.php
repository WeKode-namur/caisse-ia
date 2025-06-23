<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Models\{Transaction, TransactionItem, TransactionStockMovement, Customer, Company, Variant, Stock};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Crée une nouvelle transaction
     */
    public function create(Request $request)
    {
        $validationRules = [
            'transaction_type' => 'required|in:ticket,invoice',
            'notes' => 'nullable|string|max:1000',
            'is_wix_release' => 'boolean'
        ];

        // Ajouter la validation des clients seulement si la gestion est activée
        if (config('app.register_customer_management', false)) {
            $validationRules['customer_id'] = 'nullable|exists:customers,id';
            $validationRules['company_id'] = 'nullable|exists:companies,id';
        }

        $request->validate($validationRules);

        // Récupérer le panier et vérifications
        $cart = session('register_cart', []);
        $discounts = session('register_discounts', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Le panier est vide'
            ], 422);
        }

        // Vérifier la caisse
        $cashRegisterId = session('current_cash_register_id');
        if (!$cashRegisterId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune caisse sélectionnée'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $transactionData = [
                'transaction_number' => Transaction::generateTransactionNumber($request->transaction_type),
                'transaction_type' => $request->transaction_type,
                'is_wix_release' => $request->is_wix_release ?? false,
                'cashier_id' => auth()->id(),
                'cash_register_id' => $cashRegisterId,
                'currency' => 'EUR',
                'notes' => $request->notes,
                'status' => 'completed',
                'payment_status' => 'pending'
            ];

            // Ajouter les clients seulement si la gestion est activée
            if (config('app.register_customer_management', false)) {
                $transactionData['customer_id'] = $request->customer_id;
                $transactionData['company_id'] = $request->company_id;
            }

            // Créer la transaction
            $transaction = Transaction::create($transactionData);

            // Ajouter les articles
            foreach ($cart as $cartItem) {
                $this->addItemToTransaction($transaction, $cartItem);
            }

            // Appliquer les remises
            foreach ($discounts as $discount) {
                $this->applyDiscountToTransaction($transaction, $discount);
            }

            // Calculer les totaux
            $transaction->calculateTotals();

            // Vider le panier
            session()->forget(['register_cart', 'register_discounts']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction créée avec succès',
                'transaction' => [
                    'id' => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                    'transaction_type' => $transaction->transaction_type,
                    'total_amount' => $transaction->total_amount,
                    'status' => $transaction->status,
                    'payment_status' => $transaction->payment_status,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recherche une transaction pour retour
     */
    public function findTransaction($transactionNumber)
    {
        $transaction = Transaction::with(['items.variant.article', 'customer', 'company'])
            ->where('transaction_number', $transactionNumber)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction non trouvée'
            ], 404);
        }

        if (!$transaction->canBeReturned()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette transaction ne peut pas être retournée'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'transaction' => [
                'id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number,
                'transaction_type' => $transaction->transaction_type,
                'total_amount' => $transaction->total_amount,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                'customer' => $transaction->buyer ? [
                    'name' => $transaction->buyer->first_name ?? $transaction->buyer->name,
                    'email' => $transaction->buyer->email
                ] : null,
                'items' => $transaction->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'article_name' => $item->article_name,
                        'variant_reference' => $item->variant_reference,
                        'quantity' => $item->quantity,
                        'unit_price_ttc' => $item->unit_price_ttc,
                        'total_price_ttc' => $item->total_price_ttc,
                        'can_be_returned' => true // À améliorer selon votre logique
                    ];
                })
            ]
        ]);
    }

    /**
     * Traite un retour
     */
    public function processReturn(Request $request)
    {
        $request->validate([
            'original_transaction_id' => 'required|exists:transactions,id',
            'return_items' => 'required|array',
            'return_items.*.item_id' => 'required|exists:transaction_items,id',
            'return_items.*.quantity' => 'required|numeric|min:0.001',
            'return_reason' => 'required|string|max:255',
            'return_type' => 'required|in:exchange,refund,credit'
        ]);

        $originalTransaction = Transaction::findOrFail($request->original_transaction_id);

        if (!$originalTransaction->canBeReturned()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette transaction ne peut pas être retournée'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Créer la transaction de retour
            $returnTransaction = Transaction::create([
                'transaction_number' => Transaction::generateTransactionNumber('return'),
                'transaction_type' => 'return',
                'parent_transaction_id' => $originalTransaction->id,
                'return_reason' => $request->return_reason,
                'return_type' => $request->return_type,
                'customer_id' => $originalTransaction->customer_id,
                'company_id' => $originalTransaction->company_id,
                'cashier_id' => auth()->id(),
                'cash_register_id' => session('current_cash_register_id'),
                'currency' => 'EUR',
                'status' => 'completed',
                'payment_status' => 'paid'
            ]);

            $totalReturnAmount = 0;

            // Traiter chaque article retourné
            foreach ($request->return_items as $returnItem) {
                $originalItem = TransactionItem::findOrFail($returnItem['item_id']);
                $returnQuantity = $returnItem['quantity'];

                // Vérifier que la quantité n'excède pas l'original
                if ($returnQuantity > $originalItem->quantity) {
                    throw new \Exception("Quantité de retour trop élevée pour {$originalItem->article_name}");
                }

                // Créer l'article de retour (quantités négatives)
                $returnTransactionItem = TransactionItem::create([
                    'transaction_id' => $returnTransaction->id,
                    'variant_id' => $originalItem->variant_id,
                    'stock_id' => $originalItem->stock_id,
                    'article_name' => $originalItem->article_name,
                    'variant_reference' => $originalItem->variant_reference,
                    'variant_attributes' => $originalItem->variant_attributes,
                    'barcode' => $originalItem->barcode,
                    'quantity' => -$returnQuantity, // Négatif pour le retour
                    'unit_price_ht' => $originalItem->unit_price_ht,
                    'unit_price_ttc' => $originalItem->unit_price_ttc,
                    'total_price_ht' => -($originalItem->unit_price_ht * $returnQuantity),
                    'total_price_ttc' => -($originalItem->unit_price_ttc * $returnQuantity),
                    'tax_rate' => $originalItem->tax_rate,
                    'tax_amount' => -($originalItem->tax_amount * ($returnQuantity / $originalItem->quantity)),
                    'total_cost' => -($originalItem->total_cost * ($returnQuantity / $originalItem->quantity))
                ]);

                // Restaurer le stock
                $this->restoreStock($originalItem, $returnQuantity);

                $totalReturnAmount += abs($returnTransactionItem->total_price_ttc);
            }

            // Calculer les totaux de la transaction de retour
            $returnTransaction->calculateTotals();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Retour traité avec succès',
                'return_transaction' => [
                    'id' => $returnTransaction->id,
                    'transaction_number' => $returnTransaction->transaction_number,
                    'return_type' => $returnTransaction->return_type,
                    'total_amount' => abs($returnTransaction->total_amount),
                    'created_at' => $returnTransaction->created_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement du retour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Annule une transaction
     */
    public function void(Request $request, Transaction $transaction)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        if (!$transaction->canBeModified()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette transaction ne peut pas être annulée'
            ], 422);
        }

        try {
            $transaction->void($request->reason, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Transaction annulée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Traite une sortie Wix (décompte stock sans vente)
     */
    public function wixRelease(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.variant_id' => 'required|exists:variants,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Créer une transaction spéciale Wix
            $transaction = Transaction::create([
                'transaction_number' => Transaction::generateTransactionNumber('ticket'),
                'transaction_type' => 'ticket',
                'is_wix_release' => true,
                'cashier_id' => auth()->id(),
                'cash_register_id' => session('current_cash_register_id'),
                'currency' => 'EUR',
                'notes' => $request->notes ?? 'Sortie Wix - Vente en ligne',
                'status' => 'completed',
                'payment_status' => 'paid',
                'total_amount' => 0 // Pas de montant pour les sorties Wix
            ]);

            foreach ($request->items as $item) {
                $variant = Variant::with('stocks')->findOrFail($item['variant_id']);
                $quantity = $item['quantity'];

                // Vérifier le stock
                $availableStock = $variant->stocks->sum('quantity');
                if ($quantity > $availableStock) {
                    throw new \Exception("Stock insuffisant pour {$variant->article->name}");
                }

                // Créer l'item (prix à 0 pour les sorties Wix)
                $transactionItem = TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'variant_id' => $variant->id,
                    'stock_id' => $variant->stocks->first()->id,
                    'article_name' => $variant->article->name,
                    'variant_reference' => $variant->reference,
                    'barcode' => $variant->barcode,
                    'quantity' => $quantity,
                    'unit_price_ht' => 0,
                    'unit_price_ttc' => 0,
                    'total_price_ht' => 0,
                    'total_price_ttc' => 0,
                    'tax_rate' => $variant->article->tva ?? 21,
                    'tax_amount' => 0,
                    'total_cost' => 0
                ]);

                // Décompter le stock (FIFO)
                $this->deductStock($variant, $quantity, $transactionItem);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sortie Wix enregistrée avec succès',
                'transaction' => [
                    'id' => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                    'items_count' => count($request->items)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sortie Wix: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère un ticket/facture pour impression
     */
    public function print(Transaction $transaction)
    {
        $transaction->load(['items', 'payments.paymentMethod', 'customer', 'company', 'cashier']);

        return response()->json([
            'success' => true,
            'print_data' => [
                'transaction' => [
                    'id' => $transaction->id,
                    'number' => $transaction->transaction_number,
                    'type' => $transaction->transaction_type,
                    'date' => $transaction->created_at->format('d/m/Y H:i'),
                    'cashier' => $transaction->cashier->first_name . ' ' . $transaction->cashier->last_name,
                    'notes' => $transaction->notes
                ],
                'customer' => $transaction->buyer ? [
                    'name' => $transaction->buyer->first_name ?? $transaction->buyer->name,
                    'email' => $transaction->buyer->email
                ] : null,
                'items' => $transaction->items->map(function($item) {
                    return [
                        'name' => $item->article_name,
                        'reference' => $item->variant_reference,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price_ttc,
                        'total_price' => $item->total_price_ttc,
                        'tax_rate' => $item->tax_rate
                    ];
                }),
                'totals' => [
                    'subtotal_ht' => $transaction->subtotal_ht,
                    'tax_amount' => $transaction->tax_amount,
                    'discount_amount' => $transaction->discount_amount,
                    'total_amount' => $transaction->total_amount
                ],
                'payments' => $transaction->payments->map(function($payment) {
                    return [
                        'method' => $payment->paymentMethod->name,
                        'amount' => $payment->amount,
                        'reference' => $payment->reference
                    ];
                })
            ]
        ]);
    }

    /**
     * Crée une transaction à partir de la session de caisse actuelle
     */
    public function createFromCart(Request $request)
    {
        $sessionData = \App\Services\RegisterSessionService::exportSessionData();

        if (empty($sessionData['cart'])) {
            return response()->json(['success' => false, 'message' => 'Le panier est vide.'], 400);
        }

        try {
            DB::beginTransaction();
            
            // Créer la transaction principale
            $transaction = Transaction::create([
                'cashier_id' => auth()->id(),
                'cash_register_id' => \App\Services\RegisterSessionService::getCurrentCashRegister(),
                'customer_id' => $sessionData['customer']['id'] ?? null,
                'transaction_type' => 'ticket',
                'total_amount' => $sessionData['totals']['total'],
                'items_count' => $sessionData['totals']['items_count'],
                'payment_status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Ajouter les items
            foreach ($sessionData['cart'] as $item) {
                // S'assurer que les données sont correctes
                $itemData = $item;
                unset($itemData['id'], $itemData['added_at']); // Retirer les clés non pertinentes
                $transaction->items()->create($itemData);
            }
            
            DB::commit();

            return response()->json(['success' => true, 'transaction' => $transaction]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de la transaction depuis le panier: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
        }
    }

    // ===== MÉTHODES PRIVÉES =====

    /**
     * Ajoute un article à la transaction
     */
    private function addItemToTransaction(Transaction $transaction, array $cartItem)
    {
        $variant = Variant::findOrFail($cartItem['variant_id']);

        $transactionItem = TransactionItem::create([
            'transaction_id' => $transaction->id,
            'variant_id' => $cartItem['variant_id'],
            'stock_id' => $cartItem['stock_id'],
            'article_name' => $cartItem['article_name'],
            'variant_reference' => $cartItem['variant_reference'],
            'variant_attributes' => $cartItem['attributes'] ?? null,
            'barcode' => $cartItem['barcode'] ?? null,
            'quantity' => $cartItem['quantity'],
            'unit_price_ht' => $cartItem['unit_price'] / 1.21, // Simplifié
            'unit_price_ttc' => $cartItem['unit_price'],
            'total_price_ht' => ($cartItem['unit_price'] / 1.21) * $cartItem['quantity'],
            'total_price_ttc' => $cartItem['total_price'],
            'tax_rate' => $cartItem['tax_rate'],
            'tax_amount' => $cartItem['total_price'] - (($cartItem['unit_price'] / 1.21) * $cartItem['quantity']),
            'total_cost' => $cartItem['cost_price'] * $cartItem['quantity']
        ]);

        // Décompter le stock
        $this->deductStock($variant, $cartItem['quantity'], $transactionItem);
    }

    /**
     * Applique une remise à la transaction
     */
    private function applyDiscountToTransaction(Transaction $transaction, array $discount)
    {
        TransactionDiscount::create([
            'transaction_id' => $transaction->id,
            'discount_id' => $discount['discount_id'] ?? null,
            'discount_type' => $discount['type'],
            'discount_name' => $discount['name'],
            'discount_code' => $discount['code'] ?? null,
            'discount_value' => $discount['value'],
            'discount_amount' => $discount['amount'],
            'applied_to' => $discount['applied_to'] ?? 'total',
            'target_item_id' => $discount['target_item_id'] ?? null,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Déduit le stock selon la méthode FIFO
     */
    private function deductStock(Variant $variant, $quantity, TransactionItem $transactionItem)
    {
        $remainingQuantity = $quantity;

        // Récupérer les stocks par ordre FIFO (plus anciens en premier)
        $stocks = $variant->stocks()
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')
            ->orderBy('created_at')
            ->get();

        foreach ($stocks as $stock) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $quantityToUse = min($remainingQuantity, $stock->quantity);

            // Créer le mouvement de stock
            TransactionStockMovement::create([
                'transaction_item_id' => $transactionItem->id,
                'stock_id' => $stock->id,
                'quantity_used' => $quantityToUse,
                'cost_price' => $stock->buy_price,
                'total_cost' => $quantityToUse * $stock->buy_price,
                'lot_reference' => $stock->lot_reference
            ]);

            // Décompter du stock
            $stock->decrement('quantity', $quantityToUse);
            $remainingQuantity -= $quantityToUse;
        }

        if ($remainingQuantity > 0) {
            throw new \Exception("Stock insuffisant pour {$variant->article->name}");
        }
    }

    /**
     * Restaure le stock lors d'un retour
     */
    private function restoreStock(TransactionItem $originalItem, $returnQuantity)
    {
        // Récupérer les mouvements de stock de l'article original
        $stockMovements = $originalItem->stockMovements()
            ->orderBy('created_at')
            ->get();

        $remainingQuantity = $returnQuantity;

        foreach ($stockMovements as $movement) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $quantityToRestore = min($remainingQuantity, $movement->quantity_used);

            // Remettre en stock
            $movement->stock->increment('quantity', $quantityToRestore);
            $remainingQuantity -= $quantityToRestore;

            // Créer un mouvement de retour
            TransactionStockMovement::create([
                'transaction_item_id' => null, // Pas d'item pour les retours directs
                'stock_id' => $movement->stock_id,
                'quantity_used' => -$quantityToRestore, // Négatif pour indiquer un retour
                'cost_price' => $movement->cost_price,
                'total_cost' => -($quantityToRestore * $movement->cost_price),
                'lot_reference' => $movement->lot_reference
            ]);
        }
    }
}
