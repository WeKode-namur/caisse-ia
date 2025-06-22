<?php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ArticleController extends Controller
{
    /**
     * Détail d'un article
     */
    public function show($id)
    {
        // Récupération de l'article avec ses relations
        $article = Article::with([
            'category',
            'type',
            'subtype',
            'variants' => function($query) {
                $query->with([
                    'stocks' => function($stockQuery) {
                        $stockQuery->where('quantity', '>', 0)
                            ->orderBy('expiry_date', 'asc');
                    },
                    'medias',
                    'attributeValues.attribute'
                ]);
            }
        ])->findOrFail($id);

        // Calcul des stocks pour les variants
        $variants = $article->variants->map(function($variant) {
            $totalStock = $variant->stocks->sum('quantity');
            $variant->total_stock = $totalStock;

            // Calcul de la valeur du stock (prix d'achat moyen pondéré)
            $totalValue = 0;
            $totalQuantity = 0;

            foreach($variant->stocks as $stock) {
                $totalValue += ($stock->quantity * $stock->buy_price);
                $totalQuantity += $stock->quantity;
            }

            $variant->average_buy_price = $totalQuantity > 0 ? $totalValue / $totalQuantity : 0;
            $variant->stock_value = $totalValue;

            // Seuil d'alerte fictif (à adapter selon ta logique)
            $variant->seuil_alerte = 5; // Tu peux ajouter ce champ dans ta table variants

            return $variant;
        });

        // Récupération de l'historique des mouvements
        $mouvements = $this->getStockMovements($article, $variants);

        // Calculs pour l'article principal (si pas de variants)
        if ($variants->isEmpty()) {
            $article->stock_actuel = $this->calculateTotalStock($article->id);
            $article->valeur_stock = $this->calculateStockValue($article->id);
            $article->seuil_alerte = 10; // À adapter
        }

        // Statistiques générales
        $stats = $this->calculateArticleStats($article, $variants);

        return view('panel.inventory.show', compact(
            'article',
            'variants',
            'mouvements',
            'stats'
        ));
    }

    /**
     * Calcul du stock total pour un article simple (sans variants)
     */
    private function calculateTotalStock($articleId)
    {
        return DB::table('stocks')
            ->join('variants', 'stocks.variant_id', '=', 'variants.id')
            ->where('variants.article_id', $articleId)
            ->sum('stocks.quantity');
    }

    /**
     * Calcul de la valeur du stock pour un article simple
     */
    private function calculateStockValue($articleId)
    {
        return DB::table('stocks')
            ->join('variants', 'stocks.variant_id', '=', 'variants.id')
            ->where('variants.article_id', $articleId)
            ->selectRaw('SUM(stocks.quantity * stocks.buy_price) as total_value')
            ->value('total_value') ?? 0;
    }

    /**
     * Récupération des mouvements de stock (simulés pour l'instant)
     */
    private function getStockMovements($article, $variants)
    {
        // Pour l'instant on simule, mais tu peux créer une table stock_movements
        // ou récupérer depuis les transaction_stock_movements
        return collect([
            (object)[
                'id' => 1,
                'type' => 'entrée',
                'quantite' => 50,
                'motif' => 'Réapprovisionnement',
                'stock_resultant' => 150,
                'created_at' => now()->subDays(2)
            ],
            (object)[
                'id' => 2,
                'type' => 'vente',
                'quantite' => -3,
                'motif' => 'Vente ticket #1234',
                'stock_resultant' => 147,
                'created_at' => now()->subHours(5)
            ],
            (object)[
                'id' => 3,
                'type' => 'sortie',
                'quantite' => -2,
                'motif' => 'Sortie Wix',
                'stock_resultant' => 145,
                'created_at' => now()->subHours(2)
            ],
            (object)[
                'id' => 4,
                'type' => 'ajustement',
                'quantite' => -1,
                'motif' => 'Correction inventaire',
                'stock_resultant' => 144,
                'created_at' => now()->subHour()
            ]
        ]);
    }

    /**
     * Calcul des statistiques de l'article
     */
    private function calculateArticleStats($article, $variants)
    {
        $stats = (object)[
            'total_stock' => 0,
            'total_value' => 0,
            'variants_count' => $variants->count(),
            'variants_in_stock' => 0,
            'variants_low_stock' => 0,
            'variants_out_of_stock' => 0,
            'average_margin' => 0
        ];

        if ($variants->isNotEmpty()) {
            $stats->total_stock = $variants->sum('total_stock');
            $stats->total_value = $variants->sum('stock_value');
            $stats->variants_in_stock = $variants->where('total_stock', '>', 0)->count();
            $stats->variants_low_stock = $variants->filter(function($v) {
                return $v->total_stock <= $v->seuil_alerte && $v->total_stock > 0;
            })->count();
            $stats->variants_out_of_stock = $variants->where('total_stock', 0)->count();
        } else {
            $stats->total_stock = $article->stock_actuel ?? 0;
            $stats->total_value = $article->valeur_stock ?? 0;
        }

        // Calcul de la marge moyenne
        if ($article->buy_price && $article->sell_price) {
            $stats->average_margin = (($article->sell_price - $article->buy_price) / $article->sell_price) * 100;
        }

        return $stats;
    }

    /**
     * Récupération des variants en AJAX
     */
    public function getVariants($id)
    {
        $article = Article::findOrFail($id);

        $variants = $article->variants()->with([
            'stocks' => function($query) {
                $query->where('quantity', '>', 0)->orderBy('expiry_date', 'asc');
            },
            'attributeValues.attribute',
            'medias'
        ])->get();

        // Calcul des stocks pour chaque variant
        $variants = $variants->map(function($variant) {
            $totalStock = $variant->stocks->sum('quantity');
            $variant->total_stock = $totalStock;

            // Calcul de la valeur du stock
            $totalValue = 0;
            foreach($variant->stocks as $stock) {
                $totalValue += ($stock->quantity * $stock->buy_price);
            }
            $variant->stock_value = $totalValue;

            return $variant;
        });

        return view('panel.inventory.partials.article.variants-table', compact('variants'));
    }

    /**
     * Récupération de l'historique des mouvements en AJAX
     */
    public function getMovements($id)
    {
        $article = Article::findOrFail($id);

        $mouvements = collect([
            (object)[
                'id' => 1,
                'type' => 'entrée',
                'quantite' => 50,
                'motif' => 'Réapprovisionnement fournisseur ABC',
                'stock_resultant' => 150,
                'prix_unitaire' => 12.50,
                'user_name' => 'Admin',
                'variant_info' => null,
                'created_at' => now()->subDays(2)
            ],
            (object)[
                'id' => 2,
                'type' => 'vente',
                'quantite' => -3,
                'motif' => 'Vente ticket #1234',
                'stock_resultant' => 147,
                'prix_unitaire' => 25.00,
                'user_name' => 'Caissier',
                'variant_info' => 'Taille M, Rouge',
                'created_at' => now()->subHours(5)
            ],
            (object)[
                'id' => 3,
                'type' => 'sortie',
                'quantite' => -2,
                'motif' => 'Sortie Wix - Commande web #WX789',
                'stock_resultant' => 145,
                'prix_unitaire' => null,
                'user_name' => 'Système',
                'variant_info' => 'Taille L, Bleu',
                'created_at' => now()->subHours(2)
            ],
            (object)[
                'id' => 4,
                'type' => 'ajustement',
                'quantite' => -1,
                'motif' => 'Correction inventaire - Article endommagé',
                'stock_resultant' => 144,
                'prix_unitaire' => null,
                'user_name' => 'Manager',
                'variant_info' => null,
                'created_at' => now()->subHour()
            ],
            (object)[
                'id' => 5,
                'type' => 'entrée',
                'quantite' => 25,
                'motif' => 'Retour client - Article non conforme',
                'stock_resultant' => 169,
                'prix_unitaire' => 12.50,
                'user_name' => 'Service client',
                'variant_info' => 'Taille S, Noir',
                'created_at' => now()->subMinutes(30)
            ]
        ]);

        $mouvements = $mouvements->take(10);
        return view('panel.inventory.partials.article.movements-history', compact('mouvements'));
    }

    public function getVariantHistory($variantId)
    {
        $variant = Variant::findOrFail($variantId);

        // Simuler l'historique des mouvements pour ce variant
        $movements = collect([
            (object)[
                'type' => 'entrée',
                'quantity' => 20,
                'reason' => 'Réapprovisionnement',
                'date' => '15/06 14:30',
                'user' => 'Admin'
            ],
            (object)[
                'type' => 'vente',
                'quantity' => -2,
                'reason' => 'Vente ticket #1245',
                'date' => '14/06 16:45',
                'user' => 'Caissier'
            ],
            (object)[
                'type' => 'ajustement',
                'quantity' => -1,
                'reason' => 'Correction inventaire',
                'date' => '13/06 09:15',
                'user' => 'Manager'
            ],
            (object)[
                'type' => 'sortie',
                'quantity' => -3,
                'reason' => 'Sortie Wix - Commande web',
                'date' => '12/06 11:20',
                'user' => 'Système'
            ],
            (object)[
                'type' => 'retour',
                'quantity' => 1,
                'reason' => 'Retour client',
                'date' => '11/06 08:30',
                'user' => 'Service client'
            ]
        ]);

        return response()->json([
            'success' => true,
            'movements' => $movements
        ]);
    }

    /**
     * Méthode pour formater les prix (helper)
     */
    private function formatPrice($price)
    {
        return number_format($price, 2, ',', ' ') . ' €';
    }
}
