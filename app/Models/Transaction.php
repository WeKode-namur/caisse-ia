<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashier_id',
        'cash_register_id',
        'customer_id',
        'transaction_type',
        'transaction_number',
        'total_amount',
        'items_count',
        'payment_status',
        'notes',
        'is_wix_release',
        'company_id',
        'parent_transaction_id',
        'return_reason',
        'return_type',
        'subtotal_ht',
        'subtotal_ttc',
        'tax_amount',
        'discount_amount',
        'total_cost',
        'total_margin',
        'margin_percentage',
        'currency',
        'exchange_rate',
        'pos_terminal',
        'voided_by',
        'voided_at',
        'void_reason'
    ];

    protected $casts = [
        'is_wix_release' => 'boolean',
        'subtotal_ht' => 'decimal:4',
        'subtotal_ttc' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'total_cost' => 'decimal:4',
        'total_margin' => 'decimal:4',
        'margin_percentage' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'voided_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                // Déterminer le préfixe selon le type de transaction
                $prefix = match($transaction->transaction_type) {
                    'ticket' => 'T',
                    'invoice' => 'F',
                    'return' => 'R',
                    'refund' => 'RF',
                    default => 'T'
                };
                
                $date = now()->format('Ymd');
                $latestTransaction = self::where('transaction_number', 'like', $prefix . $date . '%')
                                         ->latest('transaction_number')
                                         ->first();
                
                if ($latestTransaction) {
                    $lastNumber = (int) substr($latestTransaction->transaction_number, -4);
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }

                $transaction->transaction_number = $prefix . $date . '-' . $newNumber;
            }
        });
    }

    // ===== RELATIONS =====

    /**
     * Client de la transaction
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Entreprise de la transaction
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Transaction parent (pour les retours)
     */
    public function parentTransaction()
    {
        return $this->belongsTo(Transaction::class, 'parent_transaction_id');
    }

    /**
     * Transactions enfants (retours de cette transaction)
     */
    public function childTransactions()
    {
        return $this->hasMany(Transaction::class, 'parent_transaction_id');
    }

    /**
     * Caissier ayant effectué la transaction
     */
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Caisse utilisée
     */
    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    /**
     * Utilisateur ayant annulé la transaction
     */
    public function voidedBy()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    /**
     * Articles de la transaction
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Remises appliquées
     */
    public function discounts()
    {
        return $this->hasMany(TransactionDiscount::class);
    }

    /**
     * Paiements de la transaction
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Points de fidélité liés
     */
    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    /**
     * Transactions de cartes cadeaux utilisées
     */
    public function giftCardTransactions()
    {
        return $this->hasMany(GiftCardTransaction::class);
    }

    // ===== SCOPES =====

    /**
     * Scope pour un type de transaction
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope pour les tickets
     */
    public function scopeTickets($query)
    {
        return $query->ofType('ticket');
    }

    /**
     * Scope pour les factures
     */
    public function scopeInvoices($query)
    {
        return $query->ofType('invoice');
    }

    /**
     * Scope pour les retours
     */
    public function scopeReturns($query)
    {
        return $query->ofType('return');
    }

    /**
     * Scope pour les transactions d'une période
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope pour les transactions d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope pour les transactions non Wix
     */
    public function scopeNotWixRelease($query)
    {
        return $query->where('is_wix_release', false);
    }

    // ===== ACCESSORS =====

    /**
     * Retourne le client/entreprise selon le cas
     */
    public function getBuyerAttribute()
    {
        return $this->customer ?: $this->company;
    }

    /**
     * Vérifie si c'est un retour
     */
    public function getIsReturnAttribute()
    {
        return in_array($this->transaction_type, ['return', 'refund']);
    }

    /**
     * Vérifie si la transaction est annulée
     */
    public function getIsVoidedAttribute()
    {
        return !is_null($this->voided_at);
    }

    /**
     * Vérifie si la transaction est complètement payée
     */
    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Retourne le nombre d'articles
     */
    public function getItemsCountAttribute()
    {
        return $this->items()->sum('quantity');
    }

    // ===== METHODS =====

    /**
     * Calcule les totaux de la transaction
     */
    public function calculateTotals()
    {
        $this->subtotal_ht = $this->items()->sum('total_price_ht');
        $this->subtotal_ttc = $this->items()->sum('total_price_ttc');
        $this->tax_amount = $this->items()->sum('tax_amount');
        $this->discount_amount = $this->discounts()->sum('discount_amount');
        $this->total_amount = $this->subtotal_ttc - $this->discount_amount;
        $this->total_cost = $this->items()->sum('total_cost');
        $this->total_margin = $this->total_amount - $this->total_cost;

        if ($this->total_amount > 0) {
            $this->margin_percentage = ($this->total_margin / $this->total_amount) * 100;
        }

        $this->save();
    }

    /**
     * Annule la transaction
     */
    public function void($reason = null, $voidedBy = null)
    {
        $this->status = 'cancelled';
        $this->voided_at = now();
        $this->voided_by = $voidedBy ?: auth()->id();
        $this->void_reason = $reason;
        $this->save();

        // Restaurer les stocks
        foreach ($this->items as $item) {
            foreach ($item->stockMovements as $movement) {
                $movement->stock->increment('quantity', abs($movement->quantity_used));
            }
        }

        return $this;
    }

    /**
     * Vérifie si la transaction peut être modifiée
     */
    public function canBeModified()
    {
        return !$this->is_voided &&
            $this->status === 'completed' &&
            $this->created_at->isToday();
    }

    /**
     * Vérifie si un retour peut être effectué
     */
    public function canBeReturned()
    {
        return !$this->is_voided &&
            !$this->is_return &&
            $this->status === 'completed' &&
            $this->created_at->diffInDays(now()) <= 30; // 30 jours pour retour
    }
}
