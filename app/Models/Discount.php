<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'min_amount',
        'max_discount',
        'max_uses',
        'current_uses',
        'applicable_to',
        'target_category_id',
        'target_variant_id',
        'is_active',
        'valid_from',
        'valid_until'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_amount' => 'decimal:4',
        'max_discount' => 'decimal:4',
        'max_uses' => 'integer',
        'current_uses' => 'integer',
        'is_active' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date'
    ];

    // ===== RELATIONS =====

    /**
     * Catégorie ciblée (si applicable)
     */
    public function targetCategory()
    {
        return $this->belongsTo(Category::class, 'target_category_id');
    }

    /**
     * Variant ciblé (si applicable)
     */
    public function targetVariant()
    {
        return $this->belongsTo(Variant::class, 'target_variant_id');
    }

    /**
     * Utilisations de cette remise dans les transactions
     */
    public function transactionDiscounts()
    {
        return $this->hasMany(TransactionDiscount::class);
    }

    // ===== SCOPES =====

    /**
     * Scope pour les remises actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les remises valides à une date donnée
     */
    public function scopeValidAt($query, $date = null)
    {
        $date = $date ?: now();

        return $query->where(function ($q) use ($date) {
            $q->where('valid_from', '<=', $date)
                ->where(function ($subQ) use ($date) {
                    $subQ->whereNull('valid_until')
                        ->orWhere('valid_until', '>=', $date);
                });
        });
    }

    /**
     * Scope pour les remises disponibles (pas encore épuisées)
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('max_uses')
                ->orWhereRaw('current_uses < max_uses');
        });
    }

    // ===== ACCESSORS =====

    /**
     * Vérifie si la remise est valide maintenant
     */
    public function getIsValidAttribute()
    {
        return $this->isValidAt(now());
    }

    /**
     * Vérifie si la remise est disponible (pas épuisée)
     */
    public function getIsAvailableAttribute()
    {
        return !$this->max_uses || $this->current_uses < $this->max_uses;
    }

    /**
     * Retourne le pourcentage d'utilisation
     */
    public function getUsagePercentageAttribute()
    {
        if (!$this->max_uses) {
            return 0;
        }

        return round(($this->current_uses / $this->max_uses) * 100, 2);
    }

    // ===== METHODS =====

    /**
     * Vérifie si la remise est valide à une date donnée
     */
    public function isValidAt($date)
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_from && $date < $this->valid_from) {
            return false;
        }

        if ($this->valid_until && $date > $this->valid_until) {
            return false;
        }

        return true;
    }

    /**
     * Calcule le montant de la remise pour un montant donné
     */
    public function calculateDiscount($amount)
    {
        if ($amount < $this->min_amount) {
            return 0;
        }

        switch ($this->type) {
            case 'percentage':
                $discount = $amount * ($this->value / 100);
                break;
            case 'fixed_amount':
                $discount = $this->value;
                break;
            default:
                $discount = 0;
        }

        // Appliquer la limite maximale si définie
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return min($discount, $amount);
    }

    /**
     * Incrémente le compteur d'utilisation
     */
    public function incrementUsage()
    {
        $this->increment('current_uses');
    }

    /**
     * Décrémente le compteur d'utilisation
     */
    public function decrementUsage()
    {
        if ($this->current_uses > 0) {
            $this->decrement('current_uses');
        }
    }
}
