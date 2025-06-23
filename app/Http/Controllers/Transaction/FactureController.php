<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class FactureController extends Controller
{
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

        // Calculer les totaux
        $totals = [
            'subtotal_ht' => $transaction->subtotal_ht ?? 0,
            'total_tva' => $transaction->tax_amount ?? 0,
            'subtotal_ttc' => $transaction->subtotal_ttc ?? 0,
            'total_discount' => $transaction->discount_amount ?? 0,
            'final_total' => $transaction->total_amount ?? 0
        ];

        return view('panel.factures.show', compact('transaction', 'totals'));
    }
} 