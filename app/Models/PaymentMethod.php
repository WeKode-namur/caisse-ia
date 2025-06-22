<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'icon',
        'sort_order',
        'is_active',
        'requires_reference',
        'allows_partial_payment',
        'processing_fee_percentage'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_reference' => 'boolean',
        'allows_partial_payment' => 'boolean',
        'processing_fee_percentage' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    // ===== RELATIONS =====

    /**
     * Les paiements utilisant cette méthode
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ===== SCOPES =====

    /**
     * Scope pour les méthodes actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour ordonner par ordre d'affichage
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ===== ACCESSORS =====

    /**
     * Vérifie si cette méthode nécessite une référence
     */
    public function getRequiresReferenceAttribute($value)
    {
        return (bool) $value;
    }

    /**
     * Vérifie si cette méthode permet les paiements partiels
     */
    public function getAllowsPartialPaymentAttribute($value)
    {
        return (bool) $value;
    }
}
