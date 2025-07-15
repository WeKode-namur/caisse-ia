<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SettingsSession
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Vérifier le niveau d'admin minimum (80)
        if (Auth::user()->is_admin < 80) {
            return redirect()->route('settings.index')->with('error', 'Accès refusé. Niveau d\'administrateur insuffisant.');
        }

        // Vérifier si la confirmation de mot de passe est nécessaire
        if (!$request->session()->has('settings_password_confirmed')) {
            // Autoriser l'accès à la page de confirmation (GET) et au POST de confirmation
            if (
                ($request->route()->getName() === 'settings.index' && $request->isMethod('get'))
                || $request->route()->getName() === 'settings.confirm-password'
            ) {
                return $next($request);
            }
            // Sinon, on redirige vers la page de confirmation
            return redirect()->route('settings.index');
        }

        // Si la session est confirmée, continuer
        return $next($request);
    }
}
