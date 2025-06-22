<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};

class Variant extends Model
{
    protected $fillable = [
        'article_id',
        'barcode',
        'reference',
        'sell_price',
        'buy_price',
    ];

    protected $casts = [
        'sell_price' => 'decimal:2',
        'buy_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'article parent
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Relation avec les stocks
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Relation avec les médias (images)
     */
    public function medias(): HasMany
    {
        return $this->hasMany(Media::class, 'variant_id');
    }

    /**
     * Relation avec les valeurs d'attributs (many-to-many)
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variants_attribute_value',
            'variant_id',
            'attribute_value_id'
        )->withTimestamps();
    }

    /**
     * Obtenir le stock total actuel
     */
    public function getTotalStockAttribute(): int
    {
        return $this->stocks()->sum('quantity');
    }

    /**
     * Obtenir la valeur totale du stock
     */
    public function getStockValueAttribute(): float
    {
        return $this->stocks()->sum(\DB::raw('quantity * buy_price'));
    }

    /**
     * Obtenir le prix de vente effectif (variant ou article)
     */
    public function getEffectiveSellPriceAttribute(): ?float
    {
        return $this->sell_price ?? $this->article->sell_price;
    }

    /**
     * Obtenir le prix d'achat effectif (variant ou article)
     */
    public function getEffectiveBuyPriceAttribute(): ?float
    {
        return $this->buy_price ?? $this->article->buy_price;
    }

    /**
     * Calculer la marge
     */
    public function getMarginAttribute(): ?float
    {
        $sellPrice = $this->effective_sell_price;
        $buyPrice = $this->effective_buy_price;

        if ($sellPrice && $buyPrice) {
            return $sellPrice - $buyPrice;
        }

        return null;
    }

    /**
     * Calculer le pourcentage de marge
     */
    public function getMarginPercentageAttribute(): ?float
    {
        $sellPrice = $this->effective_sell_price;
        $buyPrice = $this->effective_buy_price;

        if ($sellPrice && $buyPrice && $sellPrice > 0) {
            return (($sellPrice - $buyPrice) / $sellPrice) * 100;
        }

        return null;
    }

    /**
     * Obtenir l'affichage des attributs
     */
    public function getAttributesDisplayAttribute(): string
    {
        return $this->attributeValues
            ->map(function ($attributeValue) {
                $display = $attributeValue->attribute->name . ': ' . $attributeValue->value;

                if ($attributeValue->secondValue) {
                    $display .= ' (' . $attributeValue->secondValue . ')';
                }

                return $display;
            })
            ->implode(', ');
    }

    /**
     * Obtenir l'image principale
     */
    public function getPrimaryImageAttribute(): ?string
    {
        $image = $this->medias()->where('type', 'image')->first();
        return $image ? $image->url : null;
    }

    /**
     * Scope pour rechercher par code-barres
     */
    public function scopeByBarcode($query, string $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    /**
     * Scope pour rechercher par référence
     */
    public function scopeByReference($query, string $reference)
    {
        return $query->where('reference', 'LIKE', "%{$reference}%");
    }

    /**
     * Scope pour les variants avec stock
     */
    public function scopeWithStock($query)
    {
        return $query->whereHas('stocks', function ($q) {
            $q->where('quantity', '>', 0);
        });
    }

    /**
     * Scope pour les variants sans stock
     */
    public function scopeWithoutStock($query)
    {
        return $query->whereDoesntHave('stocks')
            ->orWhereHas('stocks', function ($q) {
                $q->where('quantity', '<=', 0);
            });
    }

    /**
     * Vérifier si le variant a du stock disponible
     */
    public function hasStock(): bool
    {
        return $this->total_stock > 0;
    }

    /**
     * Vérifier si le code-barres est unique
     */
    public static function isUniqueBarcode(string $barcode, ?int $excludeId = null): bool
    {
        $query = static::where('barcode', $barcode);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }
}
