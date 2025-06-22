<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'initial_amount',
        'remaining_amount',
        'customer_id',
        'company_id',
        'issued_by',
        'expires_at',
        'is_active',
        'design_template',
        'message'
    ];

    protected $casts = [
        'initial_amount' => 'decimal:4',
        'remaining_amount' => 'decimal:4',
        'expires_at' => 'date',
        'is_active' => 'boolean'
    ];

    // ===== RELATIONS =====

    /**
     * Client propriétaire de la carte
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Entreprise propriétaire de la carte
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Utilisateur qui a émis la carte
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Historique des transactions de la carte
     */
    public function giftCardTransactions()
    {
        return $this->hasMany(GiftCardTransaction::class)->orderBy('created_at', 'desc');
    }

    // ===== SCOPES =====

    /**
     * Scope pour les cartes actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les cartes non expirées
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now());
        });
    }

    /**
     * Scope pour les cartes avec un solde
     */
    public function scopeWithBalance($query)
    {
        return $query->where('remaining_amount', '>', 0);
    }

    /**
     * Scope pour les cartes utilisables
     */
    public function scopeUsable($query)
    {
        return $query->active()->notExpired()->withBalance();
    }

    // ===== ACCESSORS =====

    /**
     * Vérifie si la carte est expirée
     */
    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    /**
     * Vérifie si la carte est utilisable
     */
    public function getIsUsableAttribute()
    {
        return $this->is_active &&
            !$this->is_expired &&
            $this->remaining_amount > 0;
    }

    /**
     * Retourne le montant utilisé
     */
    public function getUsedAmountAttribute()
    {
        return $this->initial_amount - $this->remaining_amount;
    }

    /**
     * Retourne le pourcentage utilisé
     */
    public function getUsagePercentageAttribute()
    {
        if ($this->initial_amount == 0) {
            return 100;
        }

        return round(($this->used_amount / $this->initial_amount) * 100, 2);
    }

    /**
     * Retourne le propriétaire (client ou entreprise)
     */
    public function getOwnerAttribute()
    {
        return $this->customer ?: $this->company;
    }

    // ===== METHODS =====

    /**
     * Génère un code unique pour la carte cadeau
     */
    public static function generateUniqueCode()
    {
        do {
            $code = 'GC' . strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Utilise un montant de la carte
     */
    public function use($amount, $transactionId = null, $processedBy = null)
    {
        if (!$this->is_usable) {
            throw new \Exception('Cette carte cadeau ne peut pas être utilisée.');
        }

        if ($amount > $this->remaining_amount) {
            throw new \Exception('Montant insuffisant sur la carte cadeau.');
        }

        $balanceBefore = $this->remaining_amount;
        $this->remaining_amount -= $amount;
        $this->save();

        // Enregistrer la transaction
        $this->giftCardTransactions()->create([
            'transaction_id' => $transactionId,
            'transaction_type' => 'used',
            'amount' => -$amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->remaining_amount,
            'processed_by' => $processedBy ?: auth()->id()
        ]);

        return $this;
    }

    /**
     * Recharge la carte
     */
    public function topUp($amount, $processedBy = null)
    {
        $balanceBefore = $this->remaining_amount;
        $this->remaining_amount += $amount;
        $this->save();

        // Enregistrer la transaction
        $this->giftCardTransactions()->create([
            'transaction_type' => 'topped_up',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->remaining_amount,
            'processed_by' => $processedBy ?: auth()->id()
        ]);

        return $this;
    }

    /**
     * Désactive la carte
     */
    public function deactivate($reason = null, $processedBy = null)
    {
        $this->is_active = false;
        $this->save();

        // Enregistrer la transaction
        $this->giftCardTransactions()->create([
            'transaction_type' => 'expired',
            'amount' => 0,
            'balance_before' => $this->remaining_amount,
            'balance_after' => $this->remaining_amount,
            'notes' => $reason,
            'processed_by' => $processedBy ?: auth()->id()
        ]);

        return $this;
    }
}
