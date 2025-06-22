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
    ];

    protected $casts = [
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
}
