<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SettingsSession
{
    /**
     * Durée de validité de la session settings (en minutes)
     */
    private const SESSION_TIMEOUT = 120; // 2 heures au lieu de 30 minutes

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

        // Vérifier le niveau d'admin minimum (80) seulement pour les routes principales
        if (Auth::user()->is_admin < 80) {
            // Si c'est une requête AJAX, retourner une erreur JSON au lieu de rediriger
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Accès refusé. Niveau d\'administrateur insuffisant.',
                    'redirect' => route('settings.no-access')
                ], 403);
            }
            return redirect()->route('settings.no-access');
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

        // Vérifier l'expiration de la session
        $lastActivity = $request->session()->get('settings_last_activity');
        if ($lastActivity) {
            $lastActivityTime = Carbon::createFromTimestamp($lastActivity);
            $timeoutTime = $lastActivityTime->addMinutes(self::SESSION_TIMEOUT);

            if (Carbon::now()->isAfter($timeoutTime)) {
                // Session expirée, la supprimer
                $request->session()->forget(['settings_password_confirmed', 'settings_last_activity']);

                // Si c'est une requête AJAX, retourner une erreur JSON
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => 'Session expirée. Veuillez vous reconnecter.',
                        'redirect' => route('settings.index')
                    ], 401);
                }

                return redirect()->route('settings.index')->with('error', 'Session expirée. Veuillez vous reconnecter.');
            }
        }

        // Mettre à jour le timestamp d'activité
        $request->session()->put('settings_last_activity', Carbon::now()->timestamp);

        // Si la session est confirmée et valide, continuer
        return $next($request);
    }
}
