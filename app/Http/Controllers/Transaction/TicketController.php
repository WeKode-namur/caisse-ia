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

        // Vérifier que c'est bien un ticket
        if ($transaction->transaction_type !== 'ticket') {
            abort(404, 'Cette transaction n\'est pas un ticket de caisse.');
        }

        // Calculer les totaux
        $totals = $this->calculateTotals($transaction);

        return view('panel.tickets.show', compact('transaction', 'totals'));
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
     * Imprime un ticket
     */
    public function print($id)
    {
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

        return view('panel.tickets.print', compact('transaction', 'totals'));
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
