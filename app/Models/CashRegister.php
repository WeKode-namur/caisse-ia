<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'mac_address',
        'ip_address',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // ===== RELATIONS =====

    /**
     * Les transactions effectuées sur cette caisse
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // ===== SCOPES =====

    /**
     * Scope pour les caisses actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ===== ACCESSORS =====

    /**
     * Nom complet de la caisse avec localisation
     */
    public function getFullNameAttribute()
    {
        return $this->location ? "{$this->name} - {$this->location}" : $this->name;
    }

    // ===== METHODS =====

    /**
     * Vérifie si la caisse est en ligne (basé sur l'adresse MAC/IP)
     */
    public function isOnline()
    {
        // Logique de vérification de connexion
        // À implémenter selon vos besoins
        return $this->is_active;
    }
}
