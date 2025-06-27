<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_number',
        'gender',
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'loyalty_points',
        'loyalty_tier',
        'total_purchases',
        'marketing_consent',
        'last_visit_at',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_visit_at' => 'datetime',
        'marketing_consent' => 'boolean',
        'is_active' => 'boolean',
        'total_purchases' => 'decimal:4',
    ];

    // Constantes pour les niveaux de fidélité
    const LOYALTY_TIERS = [
        'bronze' => 'Bronze',
        'silver' => 'Argent',
        'gold' => 'Or',
        'platinum' => 'Platine',
    ];

    const GENDERS = [
        'M' => 'Masculin',
        'F' => 'Féminin',
        'Other' => 'Autre',
    ];

    /**
     * Génère automatiquement le numéro de client
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_number)) {
                $customer->customer_number = self::generateCustomerNumber();
            }
        });
    }

    /**
     * Génère un numéro de client unique
     */
    public static function generateCustomerNumber(): string
    {
        $year = date('Y');
        $lastCustomer = self::where('customer_number', 'like', "CLI-{$year}-%")
            ->orderBy('customer_number', 'desc')
            ->first();

        if ($lastCustomer) {
            $lastNumber = (int) substr($lastCustomer->customer_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('CLI-%s-%03d', $year, $newNumber);
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
     * Nom complet du client
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Vérifie si le client a des points de fidélité
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
            $this->total_purchases >= 10000 => 'platinum',
            $this->total_purchases >= 5000 => 'gold',
            $this->total_purchases >= 1000 => 'silver',
            default => 'bronze',
        };

        if ($this->loyalty_tier !== $tier) {
            $this->update(['loyalty_tier' => $tier]);
        }
    }

    /**
     * Scope pour les clients actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour rechercher par nom ou email
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('customer_number', 'like', "%{$search}%");
        });
    }
} 