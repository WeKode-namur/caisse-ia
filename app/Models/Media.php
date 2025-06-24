<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'medias';

    protected $fillable = [
        'variant_id',
        'path',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function medias()
    {
        return $this->hasMany(Media::class, 'variant_id')->where('table', 'medias');
    }

    /**
     * Relation avec le variant
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    /**
     * Obtenir l'URL publique du média
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    /**
     * Obtenir l'URL complète du média
     */
    public function getFullUrlAttribute(): string
    {
        return asset($this->url);
    }

    /**
     * Vérifier si le fichier existe
     */
    public function exists(): bool
    {
        return Storage::exists($this->path);
    }

    /**
     * Obtenir la taille du fichier en octets
     */
    public function getFileSizeAttribute(): ?int
    {
        return $this->exists() ? Storage::size($this->path) : null;
    }

    /**
     * Obtenir la taille du fichier formatée
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $size = $this->file_size;

        if (!$size) {
            return 'Inconnu';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($size) / log(1024));

        return round($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Obtenir les dimensions de l'image (si c'est une image)
     */
    public function getImageDimensionsAttribute(): ?array
    {
        if ($this->type !== 'image' || !$this->exists()) {
            return null;
        }

        try {
            $fullPath = Storage::path($this->path);
            $imageInfo = getimagesize($fullPath);

            if ($imageInfo) {
                return [
                    'width' => $imageInfo[0],
                    'height' => $imageInfo[1],
                    'mime' => $imageInfo['mime']
                ];
            }
        } catch (\Exception $e) {
            // Ignore errors
        }

        return null;
    }

    /**
     * Vérifier si c'est une image
     */
    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Obtenir le nom de fichier original
     */
    public function getFilenameAttribute(): string
    {
        return basename($this->path);
    }

    /**
     * Obtenir l'extension du fichier
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * Scope pour les images seulement
     */
    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    /**
     * Scope pour un variant spécifique
     */
    public function scopeForVariant($query, int $variantId)
    {
        return $query->where('variant_id', $variantId);
    }

    /**
     * Supprimer le média et le fichier
     */
    public function deleteWithFile(): bool
    {
        if (Storage::disk('public')->exists($this->path)) {
            Storage::disk('public')->delete($this->path);
        }
        return $this->delete();
    }

    /**
     * Événement de suppression automatique du fichier
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($media) {
            if (Storage::disk('public')->exists($media->path)) {
                Storage::disk('public')->delete($media->path);
            }
        });
    }
}
