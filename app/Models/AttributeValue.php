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
        'actif',
    ];

    protected $casts = [
        'order' => 'integer',
        'actif' => 'boolean',
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

    /**
     * Scope pour les valeurs actives uniquement
     */
    public function scopeActive($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les valeurs inactives uniquement
     */
    public function scopeInactive($query)
    {
        return $query->where('actif', false);
    }

    /**
     * Compter les articles liés à cette valeur
     */
    public function getArticlesCountAttribute()
    {
        return $this->variants()
            ->join('articles', 'variants.article_id', '=', 'articles.id')
            ->distinct('articles.id')
            ->count('articles.id');
    }

    /**
     * Compter les variants liés à cette valeur
     */
    public function getVariantsCountAttribute()
    {
        return $this->variants()->count();
    }

    /**
     * Désactiver la valeur
     */
    public function deactivate()
    {
        $this->update(['actif' => false]);
    }

    /**
     * Réactiver la valeur
     */
    public function activate()
    {
        $this->update(['actif' => true]);
    }
}
