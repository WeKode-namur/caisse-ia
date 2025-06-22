<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_PUBLISHED = 'published';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';

    protected $fillable = [
        'status', // ← Nouveau
        'reference',
        'name',
        'description',
        'category_id',
        'type_id',
        'subtype_id',
        'sell_price',
        'buy_price',
        'tva',
    ];

    protected $casts = [
        'sell_price' => 'decimal:2',
        'buy_price' => 'decimal:2',
        'tva' => 'integer',
    ];

    /**
     * Relation avec la catégorie
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation avec le type
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Relation avec le sous-type
     */
    public function subtype(): BelongsTo
    {
        return $this->belongsTo(Subtype::class);
    }

    /**
     * Relation avec les variants
     */
    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    /**
     * Obtenir le stock total de tous les variants
     */
    public function getTotalStockAttribute(): int
    {
        return $this->variants()
            ->join('stocks', 'variants.id', '=', 'stocks.variant_id')
            ->sum('stocks.quantity');
    }

    /**
     * Obtenir la valeur totale du stock
     */
    public function getStockValueAttribute(): float
    {
        return $this->variants()
            ->join('stocks', 'variants.id', '=', 'stocks.variant_id')
            ->sum(\DB::raw('stocks.quantity * stocks.buy_price'));
    }

    /**
     * Vérifier si l'article a du stock
     */
    public function hasStock(): bool
    {
        return $this->total_stock > 0;
    }

    /**
     * Obtenir le variant principal (premier ou unique)
     */
    public function primaryVariant()
    {
        return $this->variants()->first();
    }

    /**
     * Obtenir le prix de vente le plus bas parmi les variants
     */
    public function getMinSellPriceAttribute(): ?float
    {
        $variantMin = $this->variants()->whereNotNull('sell_price')->min('sell_price');
        return $variantMin ?? $this->sell_price;
    }

    /**
     * Obtenir le prix de vente le plus élevé parmi les variants
     */
    public function getMaxSellPriceAttribute(): ?float
    {
        $variantMax = $this->variants()->whereNotNull('sell_price')->max('sell_price');
        return $variantMax ?? $this->sell_price;
    }

    /**
     * Scope pour les articles actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les brouillons
     */
    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope pour rechercher par nom
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', 'LIKE', "%{$name}%");
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope pour les articles avec stock
     */
    public function scopeWithStock($query)
    {
        return $query->whereHas('variants.stocks', function ($q) {
            $q->where('quantity', '>', 0);
        });
    }

    /**
     * Scope pour les articles sans stock
     */
    public function scopeWithoutStock($query)
    {
        return $query->whereDoesntHave('variants.stocks')
            ->orWhereHas('variants.stocks', function ($q) {
                $q->havingRaw('SUM(quantity) <= 0');
            });
    }

    /**
     * Finaliser l'article (passer de draft à active)
     */
    public function finalize(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        // Vérifier qu'il y a au moins un variant
        if ($this->variants()->count() === 0) {
            throw new \Exception('L\'article doit avoir au moins un variant pour être finalisé.');
        }

        return $this->update(['status' => 'active']);
    }

    /**
     * Dupliquer l'article comme brouillon
     */
    public function duplicate(): self
    {
        $duplicate = $this->replicate();
        $duplicate->name = $this->name . ' (Copie)';
        $duplicate->status = 'draft';
        $duplicate->save();

        // Dupliquer les variants
        foreach ($this->variants as $variant) {
            $newVariant = $variant->replicate();
            $newVariant->article_id = $duplicate->id;
            $newVariant->barcode = null; // Reset barcode pour éviter les doublons
            $newVariant->save();

            // Dupliquer les attributs
            foreach ($variant->attributeValues as $attributeValue) {
                $newVariant->attributeValues()->attach($attributeValue->id);
            }

            // Dupliquer le stock initial
            foreach ($variant->stocks as $stock) {
                $newStock = $stock->replicate();
                $newStock->variant_id = $newVariant->id;
                $newStock->quantity = 0; // Reset quantity pour éviter les doublons de stock
                $newStock->save();
            }
        }

        return $duplicate;
    }
}
