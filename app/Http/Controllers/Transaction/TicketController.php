<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Affiche un ticket de caisse
     */
    public function show($id)
    {
        $query = Transaction::with(['cashier', 'items', 'payments']);
        
        // Ajouter les relations customer et company seulement si elles existent
        if (class_exists('App\Models\Customer')) {
            $query->with('customer');
        }
        if (class_exists('App\Models\Company')) {
            $query->with('company');
        }
        
        $transaction = $query->findOrFail($id);

        // Charger les vrais variants avec leurs attributs pour chaque item
        foreach ($transaction->items as $item) {
            if ($item->barcode) {
                $variant = \App\Models\Variant::with('attributeValues.attribute')
                    ->where('barcode', $item->barcode)
                    ->first();
                
                if ($variant) {
                    $item->variant = $variant;
                }
            }
        }

        // Calculer les totaux
        $totals = [
            'subtotal_ht' => $transaction->subtotal_ht ?? 0,
            'total_tva' => $transaction->tax_amount ?? 0,
            'subtotal_ttc' => $transaction->subtotal_ttc ?? 0,
            'total_discount' => $transaction->discount_amount ?? 0,
            'final_total' => $transaction->total_amount ?? 0
        ];

        // Préparer les remises pour l'affichage
        $discounts = [];
        if (!empty($transaction->discounts_data)) {
            $discounts = $transaction->discounts_data;
        } elseif ($transaction->relationLoaded('discounts')) {
            $discounts = $transaction->discounts->map(function($discount) {
                return [
                    'name' => $discount->discount_name,
                    'type' => $discount->discount_type,
                    'value' => $discount->discount_value,
                    'amount' => $discount->discount_amount,
                ];
            })->values()->toArray();
        }
        $note = $transaction->notes;

        return view('panel.tickets.show', compact('transaction', 'totals', 'discounts', 'note'));
    }

    /**
     * Calcule les totaux de la transaction
     */
    private function calculateTotals(Transaction $transaction)
    {
        $subtotalHT = 0;
        $subtotalTTC = 0;
        $totalTVA = 0;
        $totalDiscount = 0;

        foreach ($transaction->items as $item) {
            $subtotalHT += $item->total_price_ht;
            $subtotalTTC += $item->total_price_ttc;
            $totalTVA += $item->tax_amount;
            $totalDiscount += $item->discount_amount;
        }

        return [
            'subtotal_ht' => $subtotalHT,
            'subtotal_ttc' => $subtotalTTC,
            'total_tva' => $totalTVA,
            'total_discount' => $totalDiscount,
            'final_total' => $transaction->total_amount,
            'items_count' => $transaction->items_count
        ];
    }

    /**
     * Envoie le ticket par email
     */
    public function email($id, Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $withRelations = [
            'items',
            'payments.paymentMethod',
            'cashier',
        ];

        // Charger les relations client seulement si activé
        if (config('app.register_customer_management')) {
            $withRelations[] = 'customer';
            $withRelations[] = 'company';
        }

        $transaction = Transaction::with($withRelations)->findOrFail($id);

        if ($transaction->transaction_type !== 'ticket') {
            abort(404, 'Cette transaction n\'est pas un ticket de caisse.');
        }

        $totals = $this->calculateTotals($transaction);

        // TODO: Implémenter l'envoi d'email
        // Mail::to($request->email)->send(new TicketEmail($transaction, $totals));

        return back()->with('success', 'Le ticket a été envoyé par email.');
    }
}
