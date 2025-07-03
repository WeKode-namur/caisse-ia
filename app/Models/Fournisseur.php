<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'telephone',
        'email',
        'address',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class, 'fournisseur_id');
    }

    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:fournisseurs,slug' . ($id ? ",$id" : ''),
            'description' => 'nullable|string',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
        ];
    }
} 