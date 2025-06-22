<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCardTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'gift_card_id',
        'transaction_id',
        'transaction_type',
        'amount',
        'balance_before',
        'balance_after',
        'reference',
        'notes',
        'processed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'balance_before' => 'decimal:4',
        'balance_after' => 'decimal:4'
    ];

    // ===== RELATIONS =====

    /**
     * Carte cadeau associée
     */
    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }

    /**
     * Transaction de vente associée (si applicable)
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Utilisateur qui a traité l'opération
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // ===== SCOPES =====

    /**
     * Scope pour les émissions de cartes
     */
    public function scopeIssued($query)
    {
        return $query->where('transaction_type', 'issued');
    }

    /**
     * Scope pour les utilisations
     */
    public function scopeUsed($query)
    {
        return $query->where('transaction_type', 'used');
    }

    /**
     * Scope pour les rechargements
     */
    public function scopeToppedUp($query)
    {
        return $query->where('transaction_type', 'topped_up');
    }

    /**
     * Scope pour les remboursements
     */
    public function scopeRefunded($query)
    {
        return $query->where('transaction_type', 'refunded');
    }

    /**
     * Scope pour les expirations
     */
    public function scopeExpired($query)
    {
        return $query->where('transaction_type', 'expired');
    }

    /**
     * Scope pour une période donnée
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // ===== ACCESSORS =====

    /**
     * Vérifie si c'est une émission
     */
    public function getIsIssuedAttribute()
    {
        return $this->transaction_type === 'issued';
    }

    /**
     * Vérifie si c'est une utilisation
     */
    public function getIsUsedAttribute()
    {
        return $this->transaction_type === 'used';
    }

    /**
     * Vérifie si c'est un rechargement
     */
    public function getIsToppedUpAttribute()
    {
        return $this->transaction_type === 'topped_up';
    }

    /**
     * Vérifie si c'est un remboursement
     */
    public function getIsRefundedAttribute()
    {
        return $this->transaction_type === 'refunded';
    }

    /**
     * Vérifie si c'est une expiration
     */
    public function getIsExpiredAttribute()
    {
        return $this->transaction_type === 'expired';
    }

    /**
     * Retourne le type d'opération formaté
     */
    public function getFormattedTypeAttribute()
    {
        return match($this->transaction_type) {
            'issued' => 'Émission',
            'used' => 'Utilisation',
            'topped_up' => 'Rechargement',
            'refunded' => 'Remboursement',
            'expired' => 'Expiration',
            default => 'Autre'
        };
    }

    /**
     * Retourne le montant formaté avec signe
     */
    public function getFormattedAmountAttribute()
    {
        $sign = $this->amount >= 0 ? '+' : '';
        return $sign . number_format($this->amount, 2) . '€';
    }

    /**
     * Retourne une description complète de la transaction
     */
    public function getDescriptionAttribute()
    {
        $description = $this->formatted_type;

        if ($this->reference) {
            $description .= " (Réf: {$this->reference})";
        }

        if ($this->transaction) {
            $description .= " - Transaction #{$this->transaction->transaction_number}";
        }

        if ($this->notes) {
            $description .= " - {$this->notes}";
        }

        return $description;
    }

    /**
     * Vérifie si l'opération est un crédit (augmente le solde)
     */
    public function getIsCreditAttribute()
    {
        return in_array($this->transaction_type, ['issued', 'topped_up', 'refunded']);
    }

    /**
     * Vérifie si l'opération est un débit (diminue le solde)
     */
    public function getIsDebitAttribute()
    {
        return in_array($this->transaction_type, ['used', 'expired']);
    }

    // ===== METHODS =====

    /**
     * Vérifie la cohérence des soldes
     */
    public function validateBalances()
    {
        $expectedBalanceAfter = $this->balance_before + $this->amount;

        if (abs($expectedBalanceAfter - $this->balance_after) > 0.01) {
            throw new \Exception('Incohérence dans les soldes de la transaction de carte cadeau.');
        }

        return true;
    }

    /**
     * Annule la transaction (si possible)
     */
    public function reverse()
    {
        if ($this->is_used && $this->transaction) {
            throw new \Exception('Impossible d\'annuler une utilisation liée à une transaction.');
        }

        // Créer une transaction inverse
        $reverseTransaction = self::create([
            'gift_card_id' => $this->gift_card_id,
            'transaction_type' => $this->getReverseType(),
            'amount' => -$this->amount,
            'balance_before' => $this->giftCard->remaining_amount,
            'balance_after' => $this->balance_before,
            'reference' => "REVERSE-{$this->id}",
            'notes' => "Annulation de la transaction #{$this->id}",
            'processed_by' => auth()->id()
        ]);

        // Restaurer le solde
        $this->giftCard->remaining_amount = $this->balance_before;
        $this->giftCard->save();

        return $reverseTransaction;
    }

    /**
     * Retourne le type de transaction inverse
     */
    protected function getReverseType()
    {
        return match($this->transaction_type) {
            'issued' => 'expired',
            'topped_up' => 'used',
            'refunded' => 'used',
            'used' => 'topped_up',
            'expired' => 'topped_up',
            default => 'used'
        };
    }
}
