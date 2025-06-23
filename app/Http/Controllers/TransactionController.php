<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer les paramètres de filtrage
        $dateFilter = $request->get('date_filter', 'today');
        $type = $request->get('type', '');
        $status = $request->get('status', '');
        $client = $request->get('client', '');
        $minAmount = $request->get('min_amount', '');
        $maxAmount = $request->get('max_amount', '');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');

        // Construire la requête de base avec tous les filtres
        $query = Transaction::query();
        $this->applyFilters($query, $request);

        // Calculer les statistiques sur la requête filtrée AVANT la pagination
        $stats = $this->calculateStats(clone $query);

        // Pagination
        $transactions = $query->with(['cashier', 'items', 'payments'])->orderBy('created_at', 'desc')->paginate(20);
        
        if ($request->ajax()) {
            if ($request->has('stats_only')) {
                return view('panel.transactions._stats', compact('stats'))->render();
            }
            return view('panel.transactions._table', compact('transactions'))->render();
        }

        return view('panel.transactions.view', compact('transactions', 'stats'));
    }

    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request)
    {
        $dateFilter = $request->get('date_filter', 'today');
        $type = $request->get('type', '');
        $status = $request->get('status', '');
        $client = $request->get('client', '');
        $minAmount = $request->get('min_amount', '');
        $maxAmount = $request->get('max_amount', '');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');

        // Ajouter les relations customer et company seulement si elles existent
        if (class_exists('App\Models\Customer')) {
            $query->with('customer');
        }
        if (class_exists('App\Models\Company')) {
            $query->with('company');
        }

        // Filtre par date
        switch ($dateFilter) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'custom':
                if ($startDate) {
                    $query->where('created_at', '>=', $startDate . ' 00:00:00');
                }
                if ($endDate) {
                    $query->where('created_at', '<=', $endDate . ' 23:59:59');
                }
                break;
        }

        // Filtre par type
        if ($type) {
            $query->where('transaction_type', $type);
        }

        // Filtre par statut
        if ($status) {
            $query->where('payment_status', $status);
        }

        // Filtre par client
        if ($client) {
            $query->where(function($q) use ($client) {
                if (class_exists('App\Models\Customer')) {
                    $q->whereHas('customer', function($q) use ($client) {
                        $q->where('name', 'like', "%{$client}%");
                    });
                }
                if (class_exists('App\Models\Company')) {
                    $q->orWhereHas('company', function($q) use ($client) {
                        $q->where('name', 'like', "%{$client}%");
                    });
                }
            });
        }

        // Filtre par montant
        if ($minAmount) {
            $query->where('total_amount', '>=', $minAmount);
        }
        if ($maxAmount) {
            $query->where('total_amount', '<=', $maxAmount);
        }
    }

    private function calculateStats(\Illuminate\Database\Eloquent\Builder $query)
    {
        $totalTransactions = $query->count();
        $totalRevenue = $query->sum('total_amount');
        $averageTicket = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        
        // Compter les clients uniques seulement si la colonne customer_id existe
        $uniqueCustomers = 0;
        if (class_exists('App\Models\Customer')) {
            $uniqueCustomers = $query->whereNotNull('customer_id')->distinct('customer_id')->count('customer_id');
        }

        return [
            [
                'label' => 'Transactions',
                'value' => $totalTransactions,
                'icon' => 'fas fa-receipt',
                'color' => 'blue'
            ],
            [
                'label' => 'Chiffre d\'affaires',
                'value' => '€ ' . number_format($totalRevenue, 2, ',', ' '),
                'icon' => 'fas fa-euro-sign',
                'color' => 'green'
            ],
            [
                'label' => 'Ticket moyen',
                'value' => '€ ' . number_format($averageTicket, 2, ',', ' '),
                'icon' => 'fas fa-chart-line',
                'color' => 'purple'
            ],
            [
                'label' => 'Clients uniques',
                'value' => $uniqueCustomers,
                'icon' => 'fas fa-users',
                'color' => 'orange'
            ]
        ];
    }
} 