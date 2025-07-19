<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\{Article, TransactionItem, TransactionStockMovement, User};
use Illuminate\Http\Request;

class MovementHistoryController extends Controller
{
    public function index($articleId, Request $request)
    {
        $article = Article::findOrFail($articleId);
        $users = User::orderBy('name')->get();

        // Si l'article a un stock illimité, utiliser une vue différente
        if ($article->stock_no_limit) {
            return view('panel.inventory.transactions-history', compact('article', 'users'));
        }

        return view('panel.inventory.movements-history', compact('article', 'users'));
    }

    public function table($articleId, Request $request)
    {
        $article = Article::findOrFail($articleId);
        $query = TransactionStockMovement::query()
            ->whereHas('transactionItem.variant', function($q) use ($article) {
                $q->where('article_id', $article->id);
            })
            ->with(['transactionItem.variant', 'transactionItem.transaction', 'transactionItem', 'transactionItem.transaction.cashier']);

        // Filtres dynamiques
        if ($request->filled('type')) {
            if ($request->type === 'entree') {
                $query->where('quantity_used', '<', 0);
            } elseif ($request->type === 'sortie') {
                $query->where('quantity_used', '>', 0);
            }
        }
        if ($request->filled('origine')) {
            if ($request->origine === 'caisse') {
                $query->whereHas('transactionItem.transaction', function($q) {
                    $q->where('is_wix_release', false);
                });
            } elseif ($request->origine === 'eshop') {
                $query->whereHas('transactionItem.transaction', function($q) {
                    $q->where('is_wix_release', true);
                });
            }
        }
        if ($request->filled('membre')) {
            $query->whereHas('transactionItem.transaction', function($q) use ($request) {
                $q->where('cashier_id', $request->membre);
            });
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('quantite_min')) {
            $query->where('quantity_used', '>=', $request->quantite_min);
        }
        if ($request->filled('quantite_max')) {
            $query->where('quantity_used', '<=', $request->quantite_max);
        }
        // Tri
        $sort = $request->get('sort', 'created_at_desc');
        switch ($sort) {
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'quantite_asc':
                $query->orderBy('quantity_used', 'asc');
                break;
            case 'quantite_desc':
                $query->orderBy('quantity_used', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        $mouvements = $query->paginate(20);
        return view('panel.inventory.partials.article.movements-history-table', compact('mouvements'))->render();
    }

    public function transactionsTable($articleId, Request $request)
    {
        $article = Article::findOrFail($articleId);

        // Récupérer les transactions pour cet article
        $query = TransactionItem::query()
            ->whereHas('variant', function ($q) use ($article) {
                $q->where('article_id', $article->id);
            })
            ->with(['variant', 'transaction', 'transaction.cashier']);

        // Filtres dynamiques
        if ($request->filled('origine')) {
            if ($request->origine === 'caisse') {
                $query->whereHas('transaction', function ($q) {
                    $q->where('is_wix_release', false);
                });
            } elseif ($request->origine === 'eshop') {
                $query->whereHas('transaction', function ($q) {
                    $q->where('is_wix_release', true);
                });
            }
        }
        if ($request->filled('membre')) {
            $query->whereHas('transaction', function ($q) use ($request) {
                $q->where('cashier_id', $request->membre);
            });
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('quantite_min')) {
            $query->where('quantity', '>=', $request->quantite_min);
        }
        if ($request->filled('quantite_max')) {
            $query->where('quantity', '<=', $request->quantite_max);
        }

        // Tri
        $sort = $request->get('sort', 'created_at_desc');
        switch ($sort) {
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'quantite_asc':
                $query->orderBy('quantity', 'asc');
                break;
            case 'quantite_desc':
                $query->orderBy('quantity', 'desc');
                break;
            case 'prix_asc':
                $query->orderBy('total_price_ttc', 'asc');
                break;
            case 'prix_desc':
                $query->orderBy('total_price_ttc', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $transactions = $query->paginate(20);
        return view('panel.inventory.partials.article.transactions-history-table', compact('transactions'))->render();
    }
}
