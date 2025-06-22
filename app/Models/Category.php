<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relation : Une catégorie a plusieurs types
     */
    public function types(): HasMany
    {
        return $this->hasMany(Type::class);
    }

    /**
     * Relation : Une catégorie a plusieurs articles
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
