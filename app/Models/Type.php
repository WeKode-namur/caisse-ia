<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Type extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Relation : Un type appartient à une catégorie
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation : Un type a plusieurs sous-types
     */
    public function subtypes(): HasMany
    {
        return $this->hasMany(Subtype::class);
    }

    /**
     * Relation : Un type a plusieurs articles
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
