<?php

namespace App\Http\Controllers;

use App\Models\UnknownItem;
use App\Models\Variant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnknownItemsController extends Controller
{
    /**
     * Affiche la liste des articles inconnus
     */
    public function index(Request $request)
    {
        $query = UnknownItem::with(['transactionItem.transaction.cashier'])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('status')) {
            if ($request->status === 'non_regularises') {
                $query->nonRegularises();
            } elseif ($request->status === 'regularises') {
                $query->regularises();
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('note_interne', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->betweenDates($request->date_start, $request->date_end);
        }

        $unknownItems = $query->paginate(20);

        // Statistiques
        $stats = [
            'total' => UnknownItem::count(),
            'non_regularises' => UnknownItem::nonRegularises()->count(),
            'regularises' => UnknownItem::regularises()->count(),
            'aujourd_hui' => UnknownItem::betweenDates(
                now()->startOfDay(),
                now()->endOfDay()
            )->count(),
        ];

        if ($request->ajax()) {
            if ($request->has('stats_only')) {
                return view('panel.unknown-items._stats', compact('stats'))->render();
            }
            return view('panel.unknown-items._table', compact('unknownItems'))->render();
        }

        return view('panel.unknown-items.index', compact('unknownItems', 'stats'));
    }

    /**
     * Affiche un article inconnu
     */
    public function show(UnknownItem $unknownItem)
    {
        $unknownItem->load(['transactionItem.transaction.cashier', 'transactionItem.transaction.customer']);

        return view('panel.unknown-items.show', compact('unknownItem'));
    }

    /**
     * Recherche intelligente d'articles pour la régularisation
     */
    public function searchArticles(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2'
        ]);

        $search = $request->search;
        $searchTerms = explode(' ', strtolower(trim($search)));

        // Recherche dans les variants avec leurs attributs
        $variants = Variant::with(['article.category', 'attributeValues.attribute'])
            ->where(function ($query) use ($search, $searchTerms) {
                // Recherche exacte
                $query->where('barcode', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('article', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('reference', 'like', "%{$search}%");
                    })
                    ->orWhereHas('attributeValues', function ($q) use ($search) {
                        $q->where('value', 'like', "%{$search}%");
                    });

                // Recherche par mots-clés (correspondance flexible)
                foreach ($searchTerms as $term) {
                    if (strlen($term) >= 2) {
                        $query->orWhere(function ($q) use ($term) {
                            $q->where('barcode', 'like', "%{$term}%")
                                ->orWhere('reference', 'like', "%{$term}%")
                                ->orWhereHas('article', function ($articleQuery) use ($term) {
                                    $articleQuery->where('name', 'like', "%{$term}%")
                                        ->orWhere('reference', 'like', "%{$term}%");
                                })
                                ->orWhereHas('article.category', function ($catQuery) use ($term) {
                                    $catQuery->where('name', 'like', "%{$term}%");
                                })
                                ->orWhereHas('attributeValues', function ($attrQuery) use ($term) {
                                    $attrQuery->where('value', 'like', "%{$term}%");
                                });
                        });
                    }
                }
            })
            ->get()
            ->map(function ($variant) {
                // Calculer un score de pertinence
                $score = 0;
                $searchLower = strtolower($search);

                // Score pour correspondance exacte
                if (str_contains(strtolower($variant->barcode), $searchLower)) $score += 10;
                if (str_contains(strtolower($variant->reference), $searchLower)) $score += 8;
                if (str_contains(strtolower($variant->article->name), $searchLower)) $score += 6;
                if (str_contains(strtolower($variant->article->reference), $searchLower)) $score += 5;

                // Score pour correspondance partielle
                foreach ($searchTerms as $term) {
                    if (strlen($term) >= 2) {
                        if (str_contains(strtolower($variant->barcode), $term)) $score += 3;
                        if (str_contains(strtolower($variant->reference), $term)) $score += 2;
                        if (str_contains(strtolower($variant->article->name), $term)) $score += 2;
                        if (str_contains(strtolower($variant->article->reference), $term)) $score += 1;
                        if (str_contains(strtolower($variant->article->category->name ?? ''), $term)) $score += 1;

                        // Score pour les attributs
                        foreach ($variant->attributeValues as $attrValue) {
                            if (str_contains(strtolower($attrValue->value), $term)) {
                                $score += 1;
                            }
                        }
                    }
                }

                $variant->relevance_score = $score;
                return $variant;
            })
            ->sortByDesc('relevance_score')
            ->take(5)
            ->values();

        return response()->json([
            'success' => true,
            'variants' => $variants,
            'search_terms' => $searchTerms
        ]);
    }

    /**
     * Régularise un article inconnu en le liant à un article existant
     */
    public function regulariser(Request $request, UnknownItem $unknownItem)
    {
        $request->validate([
            'variant_id' => 'required|uuid|exists:variants,id',
            'note_interne' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $unknownItem->lierAArticle(
                $request->variant_id,
                $request->note_interne
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Article régularisé avec succès'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la régularisation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marque un article comme non identifiable
     */
    public function marquerNonIdentifiable(Request $request, UnknownItem $unknownItem)
    {
        $request->validate([
            'note_interne' => 'nullable|string|max:500'
        ]);

        try {
            $unknownItem->marquerNonIdentifiable($request->note_interne);

            return response()->json([
                'success' => true,
                'message' => 'Article marqué comme non identifiable'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Génère le rapport PDF des articles inconnus
     */
    public function rapportPDF(Request $request)
    {
        $request->validate([
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start'
        ]);

        $unknownItems = UnknownItem::with(['transactionItem.transaction.cashier'])
            ->betweenDates($request->date_start, $request->date_end)
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $unknownItems->count(),
            'non_regularises' => $unknownItems->where('est_regularise', false)->count(),
            'regularises' => $unknownItems->where('est_regularise', true)->count(),
            'montant_total' => $unknownItems->sum('prix'),
        ];

        // Générer le PDF (à implémenter avec DomPDF ou similaire)
        // Pour l'instant, retourner les données
        return response()->json([
            'success' => true,
            'data' => [
                'items' => $unknownItems,
                'stats' => $stats,
                'periode' => [
                    'debut' => $request->date_start,
                    'fin' => $request->date_end
                ]
            ]
        ]);
    }

    /**
     * Vérifie s'il y a des articles non régularisés (pour les alertes)
     */
    public function checkNonRegularises()
    {
        $count = UnknownItem::nonRegularises()
            ->whereDate('created_at', today())
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
            'has_alert' => $count > 0
        ]);
    }
}
