<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Liste des articles de l'inventaire
     */
    public function index(Request $request)
    {
        // Si c'est une requête AJAX, retourner seulement le tableau
        if ($request->ajax()) {
            return $this->getArticlesTable($request);
        }

        // Calculer les statistiques avec les filtres
        $stats = $this->getInventoryStats($request);

        // Données pour les filtres
        $categories = Category::orderBy('name')->get();
        $stockStatuses = [
            'in_stock' => 'En stock',
            'low_stock' => 'Stock faible',
            'out_of_stock' => 'Rupture'
        ];
        $statusOptions = [
            'active_published' => 'Actifs et Publiés',
            'active' => 'Actifs uniquement',
            'published' => 'Publiés uniquement',
            'inactive' => 'Inactifs'
        ];

        return view('panel.inventory.index', compact('stats', 'categories', 'stockStatuses', 'statusOptions'));
    }

    /**
     * Retourner le tableau des articles pour AJAX
     */
    public function getArticlesTable(Request $request)
    {
        $query = Article::with(['category', 'type', 'subtype', 'variants.stocks']);

        // Filtrage par statut (par défaut: active et published)
        $status = $request->get('status', 'active_published');
        switch ($status) {
            case 'published':
                $query->where('status', Article::STATUS_PUBLISHED);
                break;
            case 'active':
                $query->where('status', Article::STATUS_ACTIVE);
                break;
            case 'inactive':
                $query->where('status', Article::STATUS_INACTIVE);
                break;
            case 'active_published':
            default:
                $query->whereIn('status', [Article::STATUS_ACTIVE, Article::STATUS_PUBLISHED]);
                break;
        }

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('variants', function ($variant) use ($search) {
                        $variant->where('barcode', 'like', "%{$search}%")
                            ->orWhere('reference', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->whereHas('variants.stocks', function ($q) {
                        $q->where('quantity', '>', 0);
                    });
                    break;
                case 'low_stock':
                    $query->whereHas('variants', function ($q) {
                        $q->whereHas('stocks', function ($stock) {
                            $stock->where('quantity', '>', 0)->where('quantity', '<=', 5);
                        });
                    });
                    break;
                case 'out_of_stock':
                    $query->whereDoesntHave('variants.stocks', function ($q) {
                        $q->where('quantity', '>', 0);
                    });
                    break;
            }
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        if (in_array($sortBy, ['name', 'reference', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $articles = $query->paginate(20)->appends($request->query());

        return view('panel.inventory.partials.articles-table', compact('articles'))->render();
    }

    /**
     * Calculer les statistiques de l'inventaire avec filtres
     */
    private function getInventoryStats(Request $request = null)
    {
        // Base query avec filtrage par statut
        $baseQuery = Article::with('variants.stocks');

        // Appliquer les mêmes filtres que pour le tableau
        if ($request) {
            $status = $request->get('status', 'active_published');
            switch ($status) {
                case 'published':
                    $baseQuery->where('status', Article::STATUS_PUBLISHED);
                    break;
                case 'active':
                    $baseQuery->where('status', Article::STATUS_ACTIVE);
                    break;
                case 'inactive':
                    $baseQuery->where('status', Article::STATUS_INACTIVE);
                    break;
                case 'active_published':
                default:
                    $baseQuery->whereIn('status', [Article::STATUS_ACTIVE, Article::STATUS_PUBLISHED]);
                    break;
            }

            // Filtres supplémentaires
            if ($request->filled('search')) {
                $search = $request->search;
                $baseQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhereHas('variants', function ($variant) use ($search) {
                            $variant->where('barcode', 'like', "%{$search}%")
                                ->orWhere('reference', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('category_id')) {
                $baseQuery->where('category_id', $request->category_id);
            }
        } else {
            // Par défaut : active et published
            $baseQuery->whereIn('status', [Article::STATUS_ACTIVE, Article::STATUS_PUBLISHED]);
        }

        // Récupérer les articles filtrés
        $articles = $baseQuery->get();
        $totalArticles = $articles->count();

        // Valeur totale du stock
        $totalStockValue = $articles->sum(function ($article) {
            return $article->variants->sum(function ($variant) {
                return $variant->stocks->sum(function ($stock) {
                    return $stock->quantity * $stock->buy_price;
                });
            });
        });

        // Articles en stock faible
        $lowStockCount = $articles->filter(function ($article) {
            return $article->variants->some(function ($variant) {
                $totalStock = $variant->stocks->sum('quantity');
                return $totalStock > 0 && $totalStock <= 5;
            });
        })->count();

        // Articles en rupture
        $outOfStockCount = $articles->filter(function ($article) {
            return $article->variants->every(function ($variant) {
                return $variant->stocks->sum('quantity') <= 0;
            });
        })->count();

        return [
            ['label' => 'Articles total', 'value' => $totalArticles, 'icon' => 'fas fa-cubes', 'color' => 'blue'],
            ['label' => 'Valeur stock', 'value' => '€ ' . number_format($totalStockValue, 0), 'icon' => 'fas fa-euro-sign', 'color' => 'green'],
            ['label' => 'Stock faible', 'value' => $lowStockCount, 'icon' => 'fas fa-arrow-trend-down', 'color' => 'orange'],
            ['label' => 'Ruptures', 'value' => $outOfStockCount, 'icon' => 'fas fa-triangle-exclamation', 'color' => 'red']
        ];
    }
}
