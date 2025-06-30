<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_number',
        'name',
        'legal_name',
        'company_number_be',
        'vat_number',
        'company_type',
        'legal_representative',
        'email',
        'phone',
        'payment_terms',
        'credit_limit',
        'loyalty_points',
        'loyalty_tier',
        'total_purchases',
        'last_order_at',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'last_order_at' => 'datetime',
        'is_active' => 'boolean',
        'total_purchases' => 'decimal:4',
        'credit_limit' => 'decimal:4',
    ];

    // Constantes pour les types d'entreprise
    const COMPANY_TYPES = [
        'SRL' => 'SRL (Société à Responsabilité Limitée)',
        'SA' => 'SA (Société Anonyme)',
        'SC' => 'SC (Société Coopérative)',
        'SNC' => 'SNC (Société en Nom Collectif)',
        'SCS' => 'SCS (Société en Commandite Simple)',
        'SCA' => 'SCA (Société en Commandite par Actions)',
        'ASBL' => 'ASBL (Association Sans But Lucratif)',
        'Indépendant' => 'Indépendant',
        'Autre' => 'Autre',
    ];

    const LOYALTY_TIERS = [
        'bronze' => 'Bronze',
        'silver' => 'Argent',
        'gold' => 'Or',
        'platinum' => 'Platine',
    ];

    /**
     * Génère automatiquement le numéro d'entreprise
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            if (empty($company->company_number)) {
                $company->company_number = self::generateCompanyNumber();
            }
        });
    }

    /**
     * Génère un numéro d'entreprise unique
     */
    public static function generateCompanyNumber(): string
    {
        $year = date('Y');
        $lastCompany = self::where('company_number', 'like', "ENT-{$year}-%")
            ->orderBy('company_number', 'desc')
            ->first();

        if ($lastCompany) {
            $lastNumber = (int) substr($lastCompany->company_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('ENT-%s-%03d', $year, $newNumber);
    }

    /**
     * Relation avec les adresses
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    /**
     * Relation avec les points de fidélité
     */
    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    /**
     * Relation avec les transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Adresse de facturation principale
     */
    public function billingAddress(): HasOne
    {
        return $this->hasOne(CustomerAddress::class)->where('type', 'billing')->where('is_primary', true);
    }

    /**
     * Adresse de livraison principale
     */
    public function shippingAddress(): HasOne
    {
        return $this->hasOne(CustomerAddress::class)->where('type', 'shipping')->where('is_primary', true);
    }

    /**
     * Vérifie si l'entreprise a des points de fidélité
     */
    public function hasLoyaltyPoints(): bool
    {
        return $this->loyalty_points > 0;
    }

    /**
     * Met à jour les points de fidélité
     */
    public function updateLoyaltyPoints(int $points, string $type, string $description = null, ?string $expiresAt = null): void
    {
        $this->loyaltyPoints()->create([
            'points' => $points,
            'type' => $type,
            'description' => $description,
            'expires_at' => $expiresAt,
        ]);

        $this->increment('loyalty_points', $points);
        $this->updateLoyaltyTier();
    }

    /**
     * Met à jour le niveau de fidélité basé sur les achats totaux
     */
    public function updateLoyaltyTier(): void
    {
        $tier = match (true) {
            $this->total_purchases >= 50000 => 'platinum',
            $this->total_purchases >= 25000 => 'gold',
            $this->total_purchases >= 10000 => 'silver',
            default => 'bronze',
        };

        if ($this->loyalty_tier !== $tier) {
            $this->update(['loyalty_tier' => $tier]);
        }
    }

    /**
     * Vérifie si l'entreprise a dépassé sa limite de crédit
     */
    public function hasExceededCreditLimit(): bool
    {
        if ($this->credit_limit <= 0) {
            return false;
        }

        $totalOwed = $this->transactions()
            ->where('status', 'pending')
            ->sum('total_amount');

        return $totalOwed > $this->credit_limit;
    }

    /**
     * Scope pour les entreprises actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour rechercher par nom ou numéro
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('legal_name', 'like', "%{$search}%")
              ->orWhere('company_number', 'like', "%{$search}%")
              ->orWhere('company_number_be', 'like', "%{$search}%")
              ->orWhere('vat_number', 'like', "%{$search}%");
        });
    }
}
