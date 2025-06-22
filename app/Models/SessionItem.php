<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionItem extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'session_id',
        'variant_id',
        'stock_id',
        'article_name',
        'variant_reference',
        'barcode',
        'quantity',
        'unit_price',
        'total_price',
        'tax_rate',
        'cost_price',
        'attributes'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:4',
        'total_price' => 'decimal:4',
        'tax_rate' => 'decimal:2',
        'cost_price' => 'decimal:4',
        'attributes' => 'array'
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
