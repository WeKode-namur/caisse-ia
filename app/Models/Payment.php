<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'payment_method_id',
        'amount',
        'currency',
        'exchange_rate',
        'reference',
        'authorization_code',
        'transaction_id_external',
        'processing_fee',
        'status',
        'failure_reason',
        'processed_at',
        'refunded_at',
        'refunded_by'
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'exchange_rate' => 'decimal:6',
        'processing_fee' => 'decimal:4',
        'processed_at' => 'datetime',
        'refunded_at' => 'datetime'
    ];

    // ===== RELATIONS =====

    /**
     * Transaction associée
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Méthode de paiement utilisée
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Utilisateur qui a effectué le remboursement
     */
    public function refundedBy()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    // ===== SCOPES =====

    /**
     * Scope pour les paiements complétés
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les paiements en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les paiements échoués
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope pour les paiements remboursés
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Scope pour une méthode de paiement
     */
    public function scopeByMethod($query, $methodCode)
    {
        return $query->whereHas('paymentMethod', function ($q) use ($methodCode) {
            $q->where('code', $methodCode);
        });
    }

    // ===== ACCESSORS =====

    /**
     * Vérifie si le paiement est complété
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifie si le paiement est en attente
     */
    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    /**
     * Vérifie si le paiement a échoué
     */
    public function getIsFailedAttribute()
    {
        return $this->status === 'failed';
    }

    /**
     * Vérifie si le paiement est remboursé
     */
    public function getIsRefundedAttribute()
    {
        return $this->status === 'refunded';
    }

    /**
     * Retourne le montant net (après frais)
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - $this->processing_fee;
    }

    /**
     * Retourne le montant en devise locale
     */
    public function getLocalAmountAttribute()
    {
        if ($this->currency === 'EUR' || $this->exchange_rate == 1) {
            return $this->amount;
        }

        return $this->amount * $this->exchange_rate;
    }

    /**
     * Vérifie si le paiement peut être remboursé
     */
    public function getCanBeRefundedAttribute()
    {
        return $this->is_completed && !$this->is_refunded;
    }

    // ===== METHODS =====

    /**
     * Marque le paiement comme complété
     */
    public function markAsCompleted($authorizationCode = null, $externalTransactionId = null)
    {
        $this->status = 'completed';
        $this->processed_at = now();

        if ($authorizationCode) {
            $this->authorization_code = $authorizationCode;
        }

        if ($externalTransactionId) {
            $this->transaction_id_external = $externalTransactionId;
        }

        $this->save();

        return $this;
    }

    /**
     * Marque le paiement comme échoué
     */
    public function markAsFailed($reason = null)
    {
        $this->status = 'failed';
        $this->failure_reason = $reason;
        $this->save();

        return $this;
    }

    /**
     * Effectue un remboursement
     */
    public function refund($refundedBy = null, $reason = null)
    {
        if (!$this->can_be_refunded) {
            throw new \Exception('Ce paiement ne peut pas être remboursé.');
        }

        $this->status = 'refunded';
        $this->refunded_at = now();
        $this->refunded_by = $refundedBy ?: auth()->id();

        if ($reason) {
            $this->failure_reason = $reason;
        }

        $this->save();

        return $this;
    }

    /**
     * Calcule les frais de traitement
     */
    public function calculateProcessingFee()
    {
        if ($this->paymentMethod && $this->paymentMethod->processing_fee_percentage > 0) {
            $this->processing_fee = $this->amount * ($this->paymentMethod->processing_fee_percentage / 100);
        } else {
            $this->processing_fee = 0;
        }

        $this->save();

        return $this->processing_fee;
    }

    /**
     * Vérifie si le paiement nécessite une référence
     */
    public function requiresReference()
    {
        return $this->paymentMethod && $this->paymentMethod->requires_reference;
    }

    /**
     * Retourne une description lisible du paiement
     */
    public function getDescription()
    {
        $description = $this->paymentMethod->name ?? 'Paiement';

        if ($this->reference) {
            $description .= " (Réf: {$this->reference})";
        }

        return $description;
    }
}
