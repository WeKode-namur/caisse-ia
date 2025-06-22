<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'cash_register_id',
        'customer_data',
        'discounts_data',
        'total_amount',
        'items_count',
        'status',
        'last_activity'
    ];

    protected $casts = [
        'customer_data' => 'array',
        'discounts_data' => 'array',
        'total_amount' => 'decimal:4',
        'items_count' => 'integer',
        'last_activity' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function sessionItems(): HasMany
    {
        return $this->hasMany(SessionItem::class, 'session_id', 'id');
    }

    /**
     * Met à jour l'activité de la session
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity' => now()]);
    }

    /**
     * Calcule et met à jour les totaux de la session
     */
    public function updateTotals(): void
    {
        $items = $this->sessionItems;
        $this->update([
            'items_count' => $items->sum('quantity'),
            'total_amount' => $items->sum('total_price')
        ]);
    }

    /**
     * Nettoie les sessions anciennes (plus de 24h)
     */
    public static function cleanOldSessions(): int
    {
        return self::where('last_activity', '<', now()->subDay())
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);
    }
} 