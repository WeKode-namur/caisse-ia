<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'company_id',
        'transaction_id',
        'points',
        'type',
        'description',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    // Types de points de fidélité
    const POINT_TYPES = [
        'earned' => 'Gagnés',
        'spent' => 'Dépensés',
        'expired' => 'Expirés',
        'bonus' => 'Bonus',
        'adjustment' => 'Ajustement',
    ];

    /**
     * Relation avec le client
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relation avec l'entreprise
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation avec la transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Vérifie si les points ont expiré
     */
    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Vérifie si les points vont expirer bientôt (dans les 30 jours)
     */
    public function isExpiringSoon(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->diffInDays(now()) <= 30;
    }

    /**
     * Scope pour les points gagnés
     */
    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    /**
     * Scope pour les points dépensés
     */
    public function scopeSpent($query)
    {
        return $query->where('type', 'spent');
    }

    /**
     * Scope pour les points non expirés
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope pour les points expirés
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope pour les points positifs
     */
    public function scopePositive($query)
    {
        return $query->where('points', '>', 0);
    }

    /**
     * Scope pour les points négatifs
     */
    public function scopeNegative($query)
    {
        return $query->where('points', '<', 0);
    }
} 