<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingsLogout
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        // DÉSACTIVÉ : La session settings ne se déconnecte plus automatiquement
        // La session ne se déconnectera que via le bouton de déconnexion ou après expiration (2h)

        // Ancien code commenté :
        // // Si l'utilisateur a une session settings active et qu'il navigue vers une route qui n'est pas dans settings
        // if ($request->session()->has('settings_password_confirmed') && !$request->is('settings*')) {
        //     // Ne déconnecter que pour les vraies navigations (pas les requêtes AJAX)
        //     // ET seulement si ce n'est pas une requête AJAX qui vient de la section settings
        //     if (!$request->ajax()) {
        //         // Supprimer la session settings
        //         $request->session()->forget(['settings_password_confirmed', 'settings_last_activity']);
        //     }
        // }

        return $next($request);
    }
}
