<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'company_id',
        'type',
        'street',
        'number',
        'city',
        'postal_code',
        'country',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // Types d'adresses
    const ADDRESS_TYPES = [
        'billing' => 'Facturation',
        'shipping' => 'Livraison',
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
     * Adresse complète formatée
     */
    public function getFullAddressAttribute(): string
    {
        return sprintf(
            '%s %s, %s %s, %s',
            $this->street,
            $this->number,
            $this->postal_code,
            $this->city,
            $this->country
        );
    }

    /**
     * Adresse sur une ligne
     */
    public function getOneLineAddressAttribute(): string
    {
        return sprintf(
            '%s %s, %s %s',
            $this->street,
            $this->number,
            $this->postal_code,
            $this->city
        );
    }

    /**
     * Définit cette adresse comme principale et retire le statut principal des autres
     */
    public function setAsPrimary(): void
    {
        // Retire le statut principal des autres adresses du même type
        $query = $this->customer_id 
            ? CustomerAddress::where('customer_id', $this->customer_id)
            : CustomerAddress::where('company_id', $this->company_id);

        $query->where('type', $this->type)
              ->where('id', '!=', $this->id)
              ->update(['is_primary' => false]);

        // Définit cette adresse comme principale
        $this->update(['is_primary' => true]);
    }

    /**
     * Scope pour les adresses de facturation
     */
    public function scopeBilling($query)
    {
        return $query->where('type', 'billing');
    }

    /**
     * Scope pour les adresses de livraison
     */
    public function scopeShipping($query)
    {
        return $query->where('type', 'shipping');
    }

    /**
     * Scope pour les adresses principales
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
} 