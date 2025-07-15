<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    protected $fillable = [
        'name',
        'type',
        'unit',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * Relation avec les valeurs d'attributs
     */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class)->orderBy('order')->orderBy('value');
    }

    /**
     * Relation avec les valeurs d'attributs actives uniquement
     */
    public function activeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class)->where('actif', true)->orderBy('order')->orderBy('value');
    }

    /**
     * Scope pour rechercher par nom
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', 'LIKE', "%{$name}%");
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les attributs actifs uniquement
     */
    public function scopeActive($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les attributs inactifs uniquement
     */
    public function scopeInactive($query)
    {
        return $query->where('actif', false);
    }

    /**
     * Compter les articles liés à cet attribut
     */
    public function getArticlesCountAttribute()
    {
        return $this->values()
            ->join('variants_attribute_value', 'attribute_values.id', '=', 'variants_attribute_value.attribute_value_id')
            ->join('variants', 'variants_attribute_value.variant_id', '=', 'variants.id')
            ->distinct('variants.article_id')
            ->count('variants.article_id');
    }

    /**
     * Compter les variants liés à cet attribut
     */
    public function getVariantsCountAttribute()
    {
        return $this->values()
            ->join('variants_attribute_value', 'attribute_values.id', '=', 'variants_attribute_value.attribute_value_id')
            ->distinct('variants_attribute_value.variant_id')
            ->count('variants_attribute_value.variant_id');
    }

    /**
     * Désactiver l'attribut
     */
    public function deactivate()
    {
        $this->update(['actif' => false]);
    }

    /**
     * Réactiver l'attribut
     */
    public function activate()
    {
        $this->update(['actif' => true]);
    }
}
