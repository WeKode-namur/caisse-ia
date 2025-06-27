<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsUnknown extends Model
{
    use HasFactory;

    protected $table = 'items_unknown';

    protected $fillable = [
        'article_name',
        'description',
        'price',
        'quantity',
        'status',
        'id_variant',
    ];
} 