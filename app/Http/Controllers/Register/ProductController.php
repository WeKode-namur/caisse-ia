<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Liste les produits pour la grille
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 25);

        // Récupérer les articles avec leurs variants en stock
        $query = Article::with(['category', 'variants.stocks'])
            ->where('name', '!=', '')
            ->whereHas('variants.stocks', function($q) {
                $q->where('quantity', '>=', 0);
            });

        // Filtrage par recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('variants', function($subQ) use ($search) {
                        $subQ->where('barcode', 'like', "%{$search}%")
                            ->orWhere('reference', 'like', "%{$search}%");
                    });
            });
        }

        // Filtrage par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $articles = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'products' => $articles->getCollection()->map(function($article) {
                return $this->formatArticle($article);
            }),
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'from' => $articles->firstItem(),
                'to' => $articles->lastItem()
            ]
        ]);
    }

    /**
     * Recherche de produits
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $query = $request->query;

        $variants = Variant::with(['article.category', 'stocks'])
            ->where(function($q) use ($query) {
                $q->where('variants.barcode', 'like', "%{$query}%")
                    ->orWhere('variants.reference', 'like', "%{$query}%")
                    ->orWhereHas('article', function($subQ) use ($query) {
                        $subQ->where('articles.name', 'like', "%{$query}%");
                    });
            })
            ->whereHas('stocks', function($q) {
                $q->where('quantity', '>', 0);
            })
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $variants->map(function($variant) {
                return $this->formatVariant($variant);
            })
        ]);
    }

    /**
     * Trouve un produit par code-barres
     */
    public function findByBarcode($barcode)
    {
        $variant = Variant::with(['article.category', 'stocks'])
            ->where('barcode', $barcode)
            ->whereHas('stocks', function($q) {
                $q->where('quantity', '>', 0);
            })
            ->first();

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé ou en rupture de stock'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => $this->formatVariant($variant, true)
        ]);
    }

    /**
     * Récupère les produits d'une catégorie
     */
    public function byCategory(Category $category)
    {
        $variants = Variant::with(['article.category', 'stocks'])
            ->whereHas('article', function($q) use ($category) {
                $q->where('category_id', $category->id);
            })
            ->whereHas('stocks', function($q) {
                $q->where('quantity', '>', 0);
            })
            ->paginate(20);

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name
            ],
            'products' => $variants->map(function($variant) {
                return $this->formatVariant($variant);
            }),
            'pagination' => [
                'current_page' => $variants->currentPage(),
                'last_page' => $variants->lastPage(),
                'total' => $variants->total()
            ]
        ]);
    }

    /**
     * Récupère les variants d'un article par son ID
     */
    public function getArticleVariants($articleId)
    {
        $article = Article::with(['variants.stocks'])->findOrFail($articleId);

        // Récupérer tous les variants en stock
        $variants = $article->variants->filter(function($variant) {
            return $variant->stocks->sum('quantity') > 0;
        });

        if ($variants->count() <= 1) {
            // Si un seul variant, le retourner directement
            $singleVariant = $variants->first();
            return response()->json([
                'success' => true,
                'single_variant' => $singleVariant ? $this->formatVariant($singleVariant, true) : null,
                'variants' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'article' => [
                'id' => $article->id,
                'name' => $article->name,
                'description' => $article->description
            ],
            'variants' => $variants->map(function($variant) {
                return $this->formatVariant($variant, true);
            })->values(),
            'total_variants' => $variants->count()
        ]);
    }

    // ===== MÉTHODES PRIVÉES =====

    /**
     * Formate un article pour l'API
     */
    private function formatArticle($article)
    {
        // Récupérer tous les variants en stock
        $variantsInStock = $article->variants->filter(function($variant) {
            return $variant->stocks->sum('quantity') > 0;
        });

        // Calculer les prix min/max
        $prices = $variantsInStock->map(function($variant) {
            return (float) $variant->sell_price;
        })->filter();

        $minPrice = $prices->min();
        $maxPrice = $prices->max();

        // Formater le prix
        $priceDisplay = $minPrice == $maxPrice ?
            number_format($minPrice, 0) . '€' :
            number_format($minPrice, 0) . '-' . number_format($maxPrice, 0) . '€';

        // Stock total
        $totalStock = $variantsInStock->sum(function($variant) {
            return $variant->stocks->sum('quantity');
        });

        // Ajout de l'image principale du premier variant en stock
        $primaryImage = null;
        $thumbnails = [];
        if ($variantsInStock->count() > 0) {
            $primaryImage = $variantsInStock->first()->primary_image;
            // Récupérer jusqu'à 4 images (toutes variants confondus)
            $thumbnails = $variantsInStock->flatMap(function($variant) {
                return $variant->medias->where('type', 'image')->pluck('url');
            })->unique()->take(4)->values()->all();
        }

        // Compter les variants en rupture de stock
        $variantsOutOfStockCount = $article->variants->filter(function($variant) {
            return $variant->stocks->sum('quantity') == 0;
        })->count();

        return [
            'id' => $article->id,
            'name' => $article->name,
            'description' => $article->description,
            'category' => $article->category ? [
                'id' => $article->category->id,
                'name' => $article->category->name
            ] : null,
            'price_display' => $priceDisplay,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'stock_quantity' => $totalStock,
            'variants_count' => $article->variants->count(),
            'variants_out_of_stock_count' => $variantsOutOfStockCount,
            'has_multiple_variants' => $article->variants->count() > 1,
            'in_stock' => $totalStock > 0,
            'primary_image' => $primaryImage,
            'thumbnails' => $thumbnails,
        ];
    }

    /**
     * Formate un variant pour l'API
     */
    private function formatVariant($variant, $detailed = false)
    {
        $data = [
            'id' => $variant->id,
            'article_id' => $variant->article_id,
            'name' => $variant->article->name,
            'reference' => $variant->reference,
            'barcode' => $variant->barcode,
            'sell_price' => (float) $variant->sell_price,
            'buy_price' => (float) $variant->buy_price,
            'category' => $variant->article->category ? [
                'id' => $variant->article->category->id,
                'name' => $variant->article->category->name
            ] : null,
            'stock_quantity' => $variant->stocks->sum('quantity'),
            'in_stock' => $variant->stocks->sum('quantity') > 0,
            'primary_image' => $variant->primary_image,
            'attributes_display' => $variant->attributes_display,
            'attributes' => $variant->attributeValues->map(function($av) {
                return [
                    'name' => $av->attribute->name,
                    'value' => $av->value,
                ];
            })->values(),
        ];

        if ($detailed) {
            $data['article'] = [
                'id' => $variant->article->id,
                'name' => $variant->article->name,
                'description' => $variant->article->description,
                'tva' => $variant->article->tva
            ];

            $data['stocks'] = $variant->stocks->map(function($stock) {
                return [
                    'id' => $stock->id,
                    'quantity' => $stock->quantity,
                    'buy_price' => (float) $stock->buy_price,
                    'lot_reference' => $stock->lot_reference,
                    'expiry_date' => $stock->expiry_date ? $stock->expiry_date->format('Y-m-d') : null
                ];
            });
        }

        return $data;
    }

    public function getVariants(Variant $variant)
    {

    }
}
