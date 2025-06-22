<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Stock extends Model
{
    protected $fillable = [
        'variant_id',
        'buy_price',
        'quantity',
        'lot_reference',
        'expiry_date',
    ];

    protected $casts = [
        'buy_price' => 'decimal:2',
        'quantity' => 'integer',
        'expiry_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec le variant
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    /**
     * Obtenir la valeur totale de ce stock
     */
    public function getTotalValueAttribute(): float
    {
        return $this->quantity * $this->buy_price;
    }

    /**
     * Vérifier si le stock a expiré
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Vérifier si le stock expire bientôt (dans les 30 jours)
     */
    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date &&
            $this->expiry_date->isFuture() &&
            $this->expiry_date->diffInDays(Carbon::now()) <= 30;
    }

    /**
     * Obtenir le statut du stock
     */
    public function getStatusAttribute(): string
    {
        if ($this->quantity <= 0) {
            return 'empty';
        }

        if ($this->is_expired) {
            return 'expired';
        }

        if ($this->is_expiring_soon) {
            return 'expiring_soon';
        }

        return 'good';
    }

    /**
     * Scope pour les stocks en rupture
     */
    public function scopeEmpty($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    /**
     * Scope pour les stocks avec quantité disponible
     */
    public function scopeAvailable($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Scope pour les stocks expirés
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', Carbon::now());
    }

    /**
     * Scope pour les stocks qui expirent bientôt
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '>', Carbon::now())
            ->where('expiry_date', '<=', Carbon::now()->addDays($days));
    }

    /**
     * Ajuster la quantité (positive = entrée, négative = sortie)
     */
    public function adjustQuantity(int $adjustment, ?string $reason = null): bool
    {
        $newQuantity = $this->quantity + $adjustment;

        if ($newQuantity < 0) {
            return false; // Pas assez de stock
        }

        $this->quantity = $newQuantity;
        return $this->save();
    }

    /**
     * Prélever du stock (sortie)
     */
    public function withdraw(int $quantity): bool
    {
        if ($this->quantity < $quantity) {
            return false;
        }

        return $this->adjustQuantity(-$quantity, 'withdrawal');
    }

    /**
     * Ajouter du stock (entrée)
     */
    public function add(int $quantity): bool
    {
        return $this->adjustQuantity($quantity, 'addition');
    }
}
