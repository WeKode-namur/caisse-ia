<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\TransactionItem;
use App\Models\TransactionStockMovement;
use App\Models\UnknownItem;
use App\Models\Variant;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnknownItemsController extends Controller
{
    /**
     * Afficher la liste des articles inconnus
     */
    public function index(Request $request)
    {
        $query = UnknownItem::with(['transaction', 'transactionItem']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('nom', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('est_regularise', false);
            } elseif ($request->status === 'regularized') {
                $query->where('est_regularise', true)
                    ->where(function ($q) {
                        $q->whereNull('note_interne')->orWhere('note_interne', 'not like', 'Non identifiable%');
                    });
            } elseif ($request->status === 'non_identifiable') {
                $query->where('est_regularise', true)
                    ->where('note_interne', 'like', 'Non identifiable%');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $unknownItems = $query->paginate(20);

        // Statistiques
        $stats = [
            'total' => UnknownItem::count(),
            'pending' => UnknownItem::where('est_regularise', false)->count(),
            'regularized' => UnknownItem::where('est_regularise', true)
                ->where(function ($q) {
                    $q->whereNull('note_interne')->orWhere('note_interne', 'not like', 'Non identifiable%');
                })->count(),
            'non_identifiable' => UnknownItem::where('est_regularise', true)
                ->where('note_interne', 'like', 'Non identifiable%')->count(),
            'total_amount' => UnknownItem::sum('prix'),
            'total_vat' => UnknownItem::sum('tva'),
        ];

        // Correction AJAX :
        if ($request->ajax() || $request->has('stats_only') || $request->has('table_only')) {
            if ($request->has('stats_only')) {
                return view('panel.settings.unknown-items._stats', compact('stats'))->render();
            }
            // Par défaut, AJAX = tableau
            return view('panel.settings.unknown-items._table', compact('unknownItems'))->render();
        }

        // Vue complète
        return view('panel.settings.unknown-items.index', compact('unknownItems', 'stats'));
    }

    /**
     * Afficher les détails d'un article inconnu
     */
    public function show(UnknownItem $unknownItem)
    {
        $unknownItem->load(['transaction', 'transactionItem']);
        return view('panel.settings.unknown-items.show', compact('unknownItem'));
    }

    /**
     * Marquer un article comme régularisé
     */
    public function regularize(Request $request, UnknownItem $unknownItem)
    {
        $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'note_interne' => 'nullable|string|max:1000',
            'deduct_stock' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Récupérer le variant
            $variant = Variant::with('stocks')->findOrFail($request->variant_id);
            $quantity = $unknownItem->transactionItem->quantity ?? 1;
            $deductStock = $request->get('deduct_stock', true); // Par défaut true pour compatibilité

            // Vérifier le stock seulement si on veut décompter
            if ($deductStock) {
                $availableStock = $variant->stocks->sum('quantity');
                if ($availableStock < $quantity) {
                    throw new Exception("Stock insuffisant pour ce variant. Disponible: {$availableStock}, Nécessaire: {$quantity}");
                }
            }

            // Préparer la note
            $stockInfo = $deductStock ? " (stock décompté)" : " (stock non décompté)";
            $note = $request->note_interne ? "Régularisé vers variant {$request->variant_id}{$stockInfo}: {$request->note_interne}" : "Régularisé vers variant {$request->variant_id}{$stockInfo}";

            // Mettre à jour l'article inconnu
            $unknownItem->update([
                'est_regularise' => true,
                'note_interne' => $note,
            ]);

            // Mettre à jour la ligne de transaction pour lier au variant
            if ($unknownItem->transactionItem) {
                $unknownItem->transactionItem->update([
                    'variant_id' => $request->variant_id,
                    'source' => 'regularised'
                ]);

                // Décompter le stock du variant (FIFO) seulement si demandé
                if ($deductStock) {
                    $this->deductStockForRegularization($variant, $quantity, $unknownItem->transactionItem);
                }
            }

            DB::commit();

            $message = $deductStock
                ? 'Article inconnu régularisé avec succès (stock décompté)'
                : 'Article inconnu régularisé avec succès (stock non décompté)';

            return response()->json([
                'success' => true,
                'message' => $message,
                'item' => $unknownItem->fresh()
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la régularisation: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Décompter le stock pour la régularisation d'un article inconnu
     */
    private function deductStockForRegularization(Variant $variant, $quantity, TransactionItem $transactionItem)
    {
        // Vérifier si l'article a l'option stock illimité
        if ($variant->article->stock_no_limit) {
            // Pour les articles avec stock illimité, ne pas décompter le stock
            return;
        }

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
            throw new Exception("Stock insuffisant pour {$variant->article->name}");
        }
    }

    /**
     * Marquer un article comme non identifiable
     */
    public function markNonIdentifiable(Request $request, UnknownItem $unknownItem)
    {
        $request->validate([
            'note_interne' => 'required|string|max:500',
        ]);

        $unknownItem->update([
            'est_regularise' => true,
            'note_interne' => "Non identifiable: {$request->note_interne}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Article marqué comme non identifiable',
            'item' => $unknownItem->fresh()
        ]);
    }

    /**
     * Supprimer un article inconnu
     */
    public function destroy(UnknownItem $unknownItem)
    {
        $unknownItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article inconnu supprimé avec succès'
        ]);
    }

    /**
     * Actions en masse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,mark_non_identifiable',
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:unknown_items,id',
        ]);

        $items = UnknownItem::whereIn('id', $request->item_ids);

        switch ($request->action) {
            case 'delete':
                $items->delete();
                $message = 'Articles inconnus supprimés avec succès.';
                break;

            case 'mark_non_identifiable':
                $request->validate([
                    'reason' => 'required|string|max:500',
                ]);

                $items->update([
                    'est_regularise' => true,
                    'note_interne' => "Non identifiable: {$request->reason}",
                ]);
                $message = 'Articles marqués comme non identifiables.';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Générer un rapport PDF
     */
    public function generateReport(Request $request)
    {
        $query = UnknownItem::with(['transactionItem.variant', 'transactionItem.transaction']);

        // Appliquer les filtres de date
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        // Appliquer les filtres de statut
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('est_regularise', false);
            } elseif ($request->status === 'regularized') {
                $query->where('est_regularise', true)
                    ->where(function ($q) {
                        $q->whereNull('note_interne')->orWhere('note_interne', 'not like', 'Non identifiable%');
                    });
            } elseif ($request->status === 'non_identifiable') {
                $query->where('est_regularise', true)
                    ->where('note_interne', 'like', 'Non identifiable%');
            }
        }

        // Appliquer le filtre de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('nom', 'like', "%{$search}%");
            });
        }

        $unknownItems = $query->orderBy('created_at', 'desc')->get();

        // Statistiques pour le rapport
        $stats = [
            'total' => $unknownItems->count(),
            'pending' => $unknownItems->where('est_regularise', false)->count(),
            'regularized' => $unknownItems->where('est_regularise', true)
                ->filter(function ($item) {
                    return !$item->note_interne || !str_starts_with($item->note_interne, 'Non identifiable');
                })->count(),
            'non_identifiable' => $unknownItems->where('est_regularise', true)
                ->filter(function ($item) {
                    return str_starts_with($item->note_interne, 'Non identifiable');
                })->count(),
            'total_amount' => $unknownItems->sum('prix'),
            'total_vat' => $unknownItems->sum('tva'),
        ];

        $pdf = Pdf::loadView('panel.settings.unknown-items.report', compact('unknownItems', 'stats'));

        return $pdf->download('rapport-articles-inconnus-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Obtenir les données pour le tableau AJAX
     */
    public function getTableData(Request $request)
    {
        $query = UnknownItem::with(['transaction', 'transactionItem']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('nom', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('est_regularise', false);
            } elseif ($request->status === 'regularized') {
                $query->where('est_regularise', true)
                    ->where(function ($q) {
                        $q->whereNull('note_interne')->orWhere('note_interne', 'not like', 'Non identifiable%');
                    });
            } elseif ($request->status === 'non_identifiable') {
                $query->where('est_regularise', true)
                    ->where('note_interne', 'like', 'Non identifiable%');
            }
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $unknownItems = $query->paginate(20);

        return response()->json([
            'data' => $unknownItems->items(),
            'pagination' => [
                'current_page' => $unknownItems->currentPage(),
                'last_page' => $unknownItems->lastPage(),
                'per_page' => $unknownItems->perPage(),
                'total' => $unknownItems->total(),
            ]
        ]);
    }

    /**
     * Obtenir les statistiques
     */
    public function getStats()
    {
        $stats = [
            'total' => UnknownItem::count(),
            'pending' => UnknownItem::where('est_regularise', false)->count(),
            'regularized' => UnknownItem::where('est_regularise', true)
                ->where(function ($q) {
                    $q->whereNull('note_interne')->orWhere('note_interne', 'not like', 'Non identifiable%');
                })->count(),
            'non_identifiable' => UnknownItem::where('est_regularise', true)
                ->where('note_interne', 'like', 'Non identifiable%')->count(),
            'total_amount' => UnknownItem::sum('prix'),
            'total_vat' => UnknownItem::sum('tva'),
            'this_month' => UnknownItem::whereMonth('created_at', now()->month)->count(),
            'this_week' => UnknownItem::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return response()->json($stats);
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

        // Recherche dans les variants avec leurs attributs et stocks
        $variants = Variant::with([
            'article.category',
            'attributeValues.attribute',
            'stocks' // Charger les stocks pour calculer le stock total
        ])
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
            ->map(function ($variant) use ($search, $searchTerms) {
                // Calculer le stock total à partir des stocks
                $totalStock = $variant->stocks->sum('quantity');

                // Calculer le prix de vente effectif (variant ou article)
                $effectiveSellPrice = $variant->sell_price ?? $variant->article->sell_price;

                // Calculer un score de pertinence amélioré
                $score = 0;
                $searchLower = strtolower($search);
                $matchedTerms = 0;
                $totalTerms = count($searchTerms);

                // Vérifier combien de termes correspondent
                foreach ($searchTerms as $term) {
                    $termMatched = false;

                    // Vérifier dans le code-barres
                    if (str_contains(strtolower($variant->barcode ?? ''), $term)) {
                        $score += 5;
                        $termMatched = true;
                    }

                    // Vérifier dans la référence
                    if (str_contains(strtolower($variant->reference ?? ''), $term)) {
                        $score += 4;
                        $termMatched = true;
                    }

                    // Vérifier dans le nom de l'article
                    if (str_contains(strtolower($variant->article->name ?? ''), $term)) {
                        $score += 3;
                        $termMatched = true;
                    }

                    // Vérifier dans la référence de l'article
                    if (str_contains(strtolower($variant->article->reference ?? ''), $term)) {
                        $score += 2;
                        $termMatched = true;
                    }

                    // Vérifier dans la catégorie
                    if (str_contains(strtolower($variant->article->category->name ?? ''), $term)) {
                        $score += 1;
                        $termMatched = true;
                    }

                    // Vérifier dans les attributs
                    foreach ($variant->attributeValues as $attrValue) {
                        if (str_contains(strtolower($attrValue->value), $term)) {
                            $score += 2;
                            $termMatched = true;
                            break; // Un seul attribut par terme
                        }
                    }

                    if ($termMatched) {
                        $matchedTerms++;
                    }
                }

                // Bonus majeur pour les correspondances qui contiennent TOUS les termes
                if ($matchedTerms === $totalTerms && $totalTerms > 1) {
                    $score += 50; // Bonus très important pour correspondance complète
                } elseif ($matchedTerms > 1) {
                    $score += ($matchedTerms / $totalTerms) * 20; // Bonus proportionnel
                }

                // Score pour correspondance exacte de la recherche complète
                if (str_contains(strtolower($variant->barcode ?? ''), $searchLower)) $score += 15;
                if (str_contains(strtolower($variant->reference ?? ''), $searchLower)) $score += 12;
                if (str_contains(strtolower($variant->article->name ?? ''), $searchLower)) $score += 10;
                if (str_contains(strtolower($variant->article->reference ?? ''), $searchLower)) $score += 8;

                // Bonus pour les articles avec des attributs qui correspondent à la recherche
                $attributeMatch = false;
                foreach ($variant->attributeValues as $attrValue) {
                    if (str_contains(strtolower($attrValue->value), $searchLower)) {
                        $score += 8;
                        $attributeMatch = true;
                        break;
                    }
                }

                // Bonus pour correspondance exacte dans les attributs
                if ($attributeMatch) {
                    $score += 5;
                }

                // Ajouter les données calculées au variant
                $variant->relevance_score = $score;
                $variant->matched_terms = $matchedTerms;
                $variant->total_terms = $totalTerms;
                $variant->total_stock = $totalStock;
                $variant->effective_sell_price = $effectiveSellPrice;

                return $variant;
            })
            ->filter(function ($variant) use ($searchTerms) {
                // Filtrer pour ne garder que les variants qui ont au moins un terme correspondant
                return $variant->matched_terms > 0;
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
     * Obtenir les variants disponibles pour la régularisation
     */
    public function getVariantsForRegularization()
    {
        $variants = Variant::with(['article', 'stocks'])
            ->whereHas('article', function ($q) {
                $q->where('status', 'active');
            })
            ->get()
            ->map(function ($variant) {
                // Calculer le stock total
                $totalStock = $variant->stocks->sum('quantity');

                // Calculer le prix de vente effectif
                $effectiveSellPrice = $variant->sell_price ?? $variant->article->sell_price;

                return [
                    'id' => $variant->id,
                    'name' => $variant->article->name . ' - ' . ($variant->reference ?? 'Sans référence'),
                    'barcode' => $variant->barcode,
                    'article_name' => $variant->article->name,
                    'reference' => $variant->reference,
                    'total_stock' => $totalStock,
                    'effective_sell_price' => $effectiveSellPrice
                ];
            });

        return response()->json($variants);
    }
}
