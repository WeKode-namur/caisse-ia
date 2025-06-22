<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'discount_id',
        'discount_type',
        'discount_name',
        'discount_code',
        'discount_value',
        'discount_amount',
        'applied_to',
        'target_item_id',
        'created_by'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:4'
    ];

    // ===== RELATIONS =====

    /**
     * Transaction associée
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Remise prédéfinie utilisée (si applicable)
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Article ciblé par la remise (si applicable)
     */
    public function targetItem()
    {
        return $this->belongsTo(TransactionItem::class, 'target_item_id');
    }

    /**
     * Utilisateur qui a appliqué la remise
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===== SCOPES =====

    /**
     * Scope pour les remises sur le total
     */
    public function scopeOnTotal($query)
    {
        return $query->where('applied_to', 'total');
    }

    /**
     * Scope pour les remises sur un article
     */
    public function scopeOnItem($query)
    {
        return $query->where('applied_to', 'item');
    }

    /**
     * Scope pour un type de remise
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('discount_type', $type);
    }

    // ===== ACCESSORS =====

    /**
     * Vérifie si c'est une remise en pourcentage
     */
    public function getIsPercentageAttribute()
    {
        return $this->discount_type === 'percentage';
    }

    /**
     * Vérifie si c'est une remise fixe
     */
    public function getIsFixedAmountAttribute()
    {
        return $this->discount_type === 'fixed_amount';
    }

    /**
     * Vérifie si c'est une remise manuelle
     */
    public function getIsManualAttribute()
    {
        return $this->discount_type === 'manual';
    }

    /**
     * Vérifie si c'est une remise de fidélité
     */
    public function getIsLoyaltyAttribute()
    {
        return $this->discount_type === 'loyalty';
    }

    /**
     * Retourne la description formatée de la remise
     */
    public function getFormattedDescriptionAttribute()
    {
        $description = $this->discount_name;

        if ($this->discount_code) {
            $description .= " (Code: {$this->discount_code})";
        }

        if ($this->is_percentage) {
            $description .= " - {$this->discount_value}%";
        } elseif ($this->is_fixed_amount) {
            $description .= " - {$this->discount_value}€";
        }

        return $description;
    }

    // ===== METHODS =====

    /**
     * Calcule le montant de la remise basé sur la valeur et le type
     */
    public function calculateDiscountAmount($baseAmount)
    {
        switch ($this->discount_type) {
            case 'percentage':
                return $baseAmount * ($this->discount_value / 100);

            case 'fixed_amount':
            case 'manual':
                return min($this->discount_value, $baseAmount);

            case 'loyalty':
                // Logique spécifique aux points de fidélité
                return $this->discount_value;

            default:
                return 0;
        }
    }

    /**
     * Applique la remise à la transaction ou à l'article
     */
    public function apply()
    {
        if ($this->applied_to === 'item' && $this->target_item_id) {
            $this->applyToItem();
        } else {
            $this->applyToTransaction();
        }
    }

    /**
     * Applique la remise à un article spécifique
     */
    protected function applyToItem()
    {
        if ($this->targetItem) {
            $discountAmount = $this->calculateDiscountAmount($this->targetItem->total_price_ttc);
            $this->discount_amount = $discountAmount;
            $this->save();

            // Mettre à jour l'article
            $this->targetItem->discount_amount += $discountAmount;
            $this->targetItem->calculateTotals();
        }
    }

    /**
     * Applique la remise au total de la transaction
     */
    protected function applyToTransaction()
    {
        $baseAmount = $this->transaction->subtotal_ttc;
        $this->discount_amount = $this->calculateDiscountAmount($baseAmount);
        $this->save();

        // Recalculer les totaux de la transaction
        $this->transaction->calculateTotals();
    }

    /**
     * Retire la remise
     */
    public function remove()
    {
        if ($this->applied_to === 'item' && $this->targetItem) {
            $this->targetItem->discount_amount -= $this->discount_amount;
            $this->targetItem->calculateTotals();
        }

        $this->delete();

        // Recalculer les totaux de la transaction
        $this->transaction->calculateTotals();
    }
}
