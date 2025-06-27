<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'variant_id',
        'stock_id',
        'article_name',
        'variant_reference',
        'variant_attributes',
        'barcode',
        'quantity',
        'unit_price_ht',
        'unit_price_ttc',
        'total_price_ht',
        'total_price_ttc',
        'tax_rate',
        'tax_amount',
        'discount_rate',
        'discount_amount',
        'total_cost',
        'margin',
        'source'
    ];

    protected $casts = [
        'variant_attributes' => 'array',
        'quantity' => 'decimal:3',
        'unit_price_ht' => 'decimal:4',
        'unit_price_ttc' => 'decimal:4',
        'total_price_ht' => 'decimal:4',
        'total_price_ttc' => 'decimal:4',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:4',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:4',
        'total_cost' => 'decimal:4',
        'margin' => 'decimal:4'
    ];

    // ===== RELATIONS =====

    /**
     * Transaction parente
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Variant du produit
     */
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    /**
     * Stock utilisé
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Mouvements de stock pour cet article
     */
    public function stockMovements()
    {
        return $this->hasMany(TransactionStockMovement::class);
    }

    /**
     * Remises appliquées à cet article
     */
    public function discounts()
    {
        return $this->hasMany(TransactionDiscount::class, 'target_item_id');
    }

    // ===== ACCESSORS =====

    /**
     * Retourne le prix unitaire avec remise
     */
    public function getFinalUnitPriceAttribute()
    {
        return $this->unit_price_ttc - ($this->discount_amount / $this->quantity);
    }

    /**
     * Retourne le prix total final (avec remise)
     */
    public function getFinalTotalPriceAttribute()
    {
        return $this->total_price_ttc - $this->discount_amount;
    }

    /**
     * Retourne le pourcentage de marge
     */
    public function getMarginPercentageAttribute()
    {
        if ($this->final_total_price <= 0) {
            return 0;
        }

        return round(($this->margin / $this->final_total_price) * 100, 2);
    }

    /**
     * Retourne les attributs formatés
     */
    public function getFormattedAttributesAttribute()
    {
        if (!$this->variant_attributes) {
            return null;
        }

        return collect($this->variant_attributes)
            ->map(fn($value, $key) => ucfirst($key) . ': ' . $value)
            ->implode(', ');
    }

    // ===== METHODS =====

    /**
     * Calcule les totaux de la ligne
     */
    public function calculateTotals()
    {
        $this->total_price_ht = $this->quantity * $this->unit_price_ht;
        $this->total_price_ttc = $this->quantity * $this->unit_price_ttc;
        $this->tax_amount = $this->total_price_ttc - $this->total_price_ht;

        // Calcul du coût total depuis les mouvements de stock
        $this->total_cost = $this->stockMovements()->sum('total_cost');
        $this->margin = $this->final_total_price - $this->total_cost;

        $this->save();
    }

    /**
     * Applique une remise à la ligne
     */
    public function applyDiscount($discountRate, $discountAmount = null)
    {
        $this->discount_rate = $discountRate;

        if ($discountAmount) {
            $this->discount_amount = min($discountAmount, $this->total_price_ttc);
        } else {
            $this->discount_amount = $this->total_price_ttc * ($discountRate / 100);
        }

        $this->calculateTotals();
    }

    /**
     * Retire la remise de la ligne
     */
    public function removeDiscount()
    {
        $this->discount_rate = 0;
        $this->discount_amount = 0;
        $this->calculateTotals();
    }
}
