<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Stock;
use App\Models\Variant;
use App\Models\TransactionStockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function store(Request $request, $articleId)
    {
        $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'quantity' => 'required|numeric|min:0.001',
            'buy_price' => 'required|numeric|min:0',
            'lot_reference' => config('custom.referent_lot_optionnel') ? 'nullable|string|max:100' : '',
            'expiry_date' => config('custom.date_expiration_optionnel') ? 'nullable|date' : '',
        ]);

        $variant = Variant::where('id', $request->variant_id)->where('article_id', $articleId)->firstOrFail();
        $quantity = $request->quantity;
        $buyPrice = $request->buy_price;
        $lotReference = $request->lot_reference;
        $expiryDate = $request->expiry_date;

        DB::beginTransaction();
        try {
            // Chercher un stock existant avec le même prix d'achat, lot et date d'expiration
            $stock = Stock::where('variant_id', $variant->id)
                ->where('buy_price', $buyPrice)
                ->when(config('custom.referent_lot_optionnel'), function($q) use ($lotReference) {
                    $q->where('lot_reference', $lotReference);
                })
                ->when(config('custom.date_expiration_optionnel'), function($q) use ($expiryDate) {
                    $q->whereDate('expiry_date', $expiryDate);
                })
                ->first();

            if ($stock) {
                // Ajouter la quantité au stock existant
                $stock->quantity += $quantity;
                $stock->save();
            } else {
                // Créer un nouveau stock
                $stock = Stock::create([
                    'variant_id' => $variant->id,
                    'buy_price' => $buyPrice,
                    'quantity' => $quantity,
                    'lot_reference' => config('custom.referent_lot_optionnel') ? $lotReference : null,
                    'expiry_date' => config('custom.date_expiration_optionnel') ? $expiryDate : null,
                ]);
            }

            // Créer la transaction technique
            $transaction = \App\Models\Transaction::create([
                'transaction_number' => 'ADJ-' . now()->format('YmdHis'),
                'transaction_type' => 'stock_adjustment',
                'status' => 'completed',
                'payment_status' => 'paid',
                'cashier_id' => auth()->id() ?? 1,
                'currency' => 'EUR',
                'notes' => 'Ajustement manuel du stock',
                'total_amount' => 0,
            ]);

            // Créer le TransactionItem technique
            $transactionItem = \App\Models\TransactionItem::create([
                'transaction_id' => $transaction->id,
                'variant_id' => $variant->id,
                'stock_id' => $stock->id,
                'article_name' => $variant->article->name,
                'variant_reference' => $variant->reference,
                'quantity' => $quantity,
                'unit_price_ht' => $buyPrice,
                'unit_price_ttc' => $buyPrice,
                'total_price_ht' => $quantity * $buyPrice,
                'total_price_ttc' => $quantity * $buyPrice,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'discount_rate' => 0,
                'discount_amount' => 0,
                'total_cost' => $quantity * $buyPrice,
                'margin' => 0,
                'source' => 'stock_adjustment',
            ]);

            // Créer le mouvement de stock (entrée)
            \App\Models\TransactionStockMovement::create([
                'transaction_item_id' => $transactionItem->id,
                'stock_id' => $stock->id,
                'quantity_used' => -$quantity, // Entrée
                'cost_price' => $buyPrice,
                'total_cost' => $quantity * $buyPrice,
                'lot_reference' => $stock->lot_reference,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Stock ajouté avec succès']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 422);
        }
    }
} 