<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Module;
use Symfony\Component\HttpFoundation\Response;

class ModuleAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si pas connecté, rediriger vers login
        if (!$user) {
            return redirect()->route('login');
        }

        $routeName = $request->route()->getName();

        // Ignorer certaines routes système
        $systemRoutes = [
            'profile.show',
            'profile.update',
            'password.update',
            'user-profile-information.update',
            'user-password.update',
            'logout',
        ];

        if (in_array($routeName, $systemRoutes)) {
            return $next($request);
        }

        // DEBUG: Afficher les informations
        if (config('app.debug')) {
            $canAccess = Module::canAccessRoute($routeName, $user);

            // Récupérer le module correspondant pour debug
            $modules = Module::where('is_enabled', true)->get();
            $matchedModule = null;

            foreach ($modules as $module) {
                $routePrefixes = array_map('trim', explode(',', $module->route_prefix ?? ''));
                foreach ($routePrefixes as $prefix) {
                    if (!empty($prefix) && (
                            str_starts_with($routeName, $prefix) ||
                            $routeName === $prefix ||
                            str_starts_with($routeName, $prefix . '.')
                        )) {
                        $matchedModule = $module;
                        break 2;
                    }
                }
            }
        }

        // Vérifier l'accès au module pour cette route
        if (!Module::canAccessRoute($routeName, $user)) {
            abort(403, "Accès non autorisé au module pour la route : {$routeName}");
        }

        if ($request->routeIs('fournisseurs.*') && !config('custom.suppliers_enabled')) {
            abort(404);
        }

        return $next($request);
    }
}
