<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionStockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_item_id',
        'stock_id',
        'quantity_used',
        'cost_price',
        'total_cost',
        'lot_reference'
    ];

    protected $casts = [
        'quantity_used' => 'decimal:3',
        'cost_price' => 'decimal:4',
        'total_cost' => 'decimal:4'
    ];

    // ===== RELATIONS =====

    /**
     * Ligne de transaction associée
     */
    public function transactionItem()
    {
        return $this->belongsTo(TransactionItem::class);
    }

    /**
     * Stock utilisé
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Transaction via la ligne
     */
    public function transaction()
    {
        return $this->hasOneThrough(
            Transaction::class,
            TransactionItem::class,
            'id',
            'id',
            'transaction_item_id',
            'transaction_id'
        );
    }

    // ===== ACCESSORS =====

    /**
     * Vérifie si c'est un mouvement de sortie
     */
    public function getIsOutgoingAttribute()
    {
        return $this->quantity_used > 0;
    }

    /**
     * Vérifie si c'est un mouvement d'entrée (retour)
     */
    public function getIsIncomingAttribute()
    {
        return $this->quantity_used < 0;
    }

    /**
     * Retourne la quantité absolue
     */
    public function getAbsoluteQuantityAttribute()
    {
        return abs($this->quantity_used);
    }

    // ===== METHODS =====

    /**
     * Calcule le coût total
     */
    public function calculateTotalCost()
    {
        $this->total_cost = abs($this->quantity_used) * $this->cost_price;
        $this->save();
    }

    /**
     * Applique le mouvement au stock
     */
    public function applyToStock()
    {
        $this->stock->decrement('quantity', $this->quantity_used);
    }

    /**
     * Annule le mouvement (restaure le stock)
     */
    public function revertFromStock()
    {
        $this->stock->increment('quantity', $this->quantity_used);
    }
}
