<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnknownItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_item_id',
        'nom',
        'description',
        'prix',
        'tva',
        'note_interne',
        'est_regularise'
    ];

    protected $casts = [
        'prix' => 'decimal:4',
        'tva' => 'integer',
        'est_regularise' => 'boolean'
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

    // ===== SCOPES =====

    /**
     * Articles non régularisés
     */
    public function scopeNonRegularises($query)
    {
        return $query->where('est_regularise', false);
    }

    /**
     * Articles régularisés
     */
    public function scopeRegularises($query)
    {
        return $query->where('est_regularise', true);
    }

    /**
     * Articles d'une période donnée
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        });
    }

    // ===== METHODS =====

    /**
     * Marquer comme régularisé
     */
    public function regulariser($note = null)
    {
        $this->update([
            'est_regularise' => true,
            'note_interne' => $note
        ]);
    }

    /**
     * Marquer comme non identifiable
     */
    public function marquerNonIdentifiable($note = null)
    {
        $this->update([
            'est_regularise' => true,
            'note_interne' => $note ? "Non identifiable: {$note}" : "Non identifiable"
        ]);
    }

    /**
     * Lier à un article existant
     */
    public function lierAArticle($variantId, $note = null)
    {
        $this->update([
            'est_regularise' => true,
            'note_interne' => $note ? "Lié à l'article {$variantId}: {$note}" : "Lié à l'article {$variantId}"
        ]);

        // Mettre à jour la ligne de transaction
        $this->transactionItem->update([
            'variant_id' => $variantId,
            'source' => 'regularised'
        ]);
    }
}
