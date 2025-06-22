<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class Module extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'icon',
        'route_prefix',
        'is_enabled',
        'is_visible_sidebar',
        'sort_order',
        'required_permission',
        'min_admin_level',
        'parent_module_id'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_visible_sidebar' => 'boolean',
        'sort_order' => 'integer',
        'min_admin_level' => 'integer',
    ];

    /**
     * Relation parent
     */
    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_module_id');
    }

    /**
     * Relation enfants
     */
    public function children()
    {
        return $this->hasMany(Module::class, 'parent_module_id')->orderBy('sort_order');
    }

    /**
     * Vérifie si l'utilisateur a accès à ce module
     */
    public function canAccess($user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        // Vérifier si le module est activé
        if (!$this->is_enabled) {
            return false;
        }

        // Vérifier le niveau d'admin requis
        if ($this->min_admin_level > 0 && $user->is_admin < $this->min_admin_level) {
            return false;
        }

        // Vérifier les permissions spécifiques (si nécessaire)
        if ($this->required_permission && !$user->hasPermission($this->required_permission)) {
            return false;
        }

        return true;
    }

    /**
     * Récupère tous les modules accessibles pour l'utilisateur avec cache
     */
    public static function getAccessibleModules($user = null): \Illuminate\Support\Collection
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return collect();
        }

        // Clé de cache basée sur l'utilisateur et la dernière modification des modules
        $cacheKey = "modules.accessible.user.{$user->id}." . static::getLastModified();

        return Cache::remember($cacheKey, 3600, function () use ($user) {
            return static::where('is_enabled', true)
                ->orderBy('sort_order')
                ->get()
                ->filter(function ($module) use ($user) {
                    return $module->canAccess($user);
                });
        });
    }

    /**
     * Récupère les modules pour la sidebar avec cache
     */
    public static function getSidebarModules($user = null): \Illuminate\Support\Collection
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return collect();
        }

        $cacheKey = "modules.sidebar.user.{$user->id}." . static::getLastModified();

        return Cache::remember($cacheKey, 3600, function () use ($user) {
            $modules = static::where('is_enabled', true)
                ->where('is_visible_sidebar', true)
                ->whereNull('parent_module_id') // Seulement les modules parents
                ->with(['children' => function ($query) {
                    $query->where('is_enabled', true)
                        ->where('is_visible_sidebar', true)
                        ->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();

            return $modules->filter(function ($module) use ($user) {
                // Vérifier l'accès au module parent
                if (!$module->canAccess($user)) {
                    return false;
                }

                // Filtrer les enfants accessibles
                $module->setRelation('children', $module->children->filter(function ($child) use ($user) {
                    return $child->canAccess($user);
                }));

                return true;
            });
        });
    }

    /**
     * Vérifie si une route est autorisée pour l'utilisateur
     */
    public static function canAccessRoute(string $routeName, $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        $cacheKey = "modules.route_access.{$routeName}.user.{$user->id}." . static::getLastModified();

        return Cache::remember($cacheKey, 3600, function () use ($routeName, $user) {
            // Trouver le module correspondant à la route
            $modules = static::where('is_enabled', true)->get();

            foreach ($modules as $module) {
                // Séparer les préfixes de route par virgule
                $routePrefixes = array_map('trim', explode(',', $module->route_prefix ?? ''));

                foreach ($routePrefixes as $prefix) {
                    // Vérifier si la route commence par le préfixe OU correspond exactement
                    if (!empty($prefix) && (
                            str_starts_with($routeName, $prefix) ||
                            $routeName === $prefix ||
                            str_starts_with($routeName, $prefix . '.')
                        )) {
                        return $module->canAccess($user);
                    }
                }
            }

            // Si aucun module ne correspond, autoriser par défaut (pour les routes système)
            return true;
        });
    }

    /**
     * Vide le cache des modules
     */
    public static function clearCache(): void
    {
        // Pour Redis, utiliser les bonnes méthodes
        try {
            // Méthode 1: Vider par pattern
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys('*modules*');
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            }

            // Méthode 2: Vider les clés connues
            $keys = [
                'modules.last_modified',
            ];

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            // Méthode 3: Vider par pattern avec Laravel
            $cacheKeys = [
                'modules.accessible.*',
                'modules.sidebar.*',
                'modules.route_access.*'
            ];

            foreach ($cacheKeys as $pattern) {
                Cache::forget($pattern);
            }

        } catch (\Exception $e) {
            // En cas d'erreur, vider tout le cache
            Cache::flush();
        }

        // Mettre à jour le timestamp de dernière modification
        Cache::forget('modules.last_modified');
        Cache::put('modules.last_modified', now(), 3600);
    }

    /**
     * Obtient le timestamp de la dernière modification
     */
    private static function getLastModified(): string
    {
        return Cache::remember('modules.last_modified', 3600, function () {
            return static::max('updated_at') ?? now();
        });
    }

    /**
     * Hook après sauvegarde pour vider le cache
     */
    protected static function booted()
    {
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Scope pour les modules activés
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope pour les modules visibles dans la sidebar
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible_sidebar', true);
    }

    /**
     * Scope pour les modules parents
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_module_id');
    }
}
