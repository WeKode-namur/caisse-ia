<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_id',
        'value',
        'second_value',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'attribut parent
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Relation avec les variants (many-to-many)
     */
    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            Variant::class,
            'variants_attribute_value',
            'attribute_value_id',
            'variant_id'
        )->withTimestamps();
    }

    /**
     * Obtenir l'affichage complet de la valeur
     */
    public function getDisplayValueAttribute(): string
    {
        $display = $this->value;

        if ($this->second_value) {
            $display .= ' (' . $this->second_value . ')';
        }

        if ($this->attribute && $this->attribute->unit) {
            $display .= ' ' . $this->attribute->unit;
        }

        return $display;
    }

    /**
     * Scope pour rechercher par valeur
     */
    public function scopeByValue($query, string $value)
    {
        return $query->where('value', 'LIKE', "%{$value}%")
            ->orWhere('second_value', 'LIKE', "%{$value}%");
    }

    /**
     * Scope pour un attribut spécifique
     */
    public function scopeForAttribute($query, int $attributeId)
    {
        return $query->where('attribute_id', $attributeId);
    }
}
