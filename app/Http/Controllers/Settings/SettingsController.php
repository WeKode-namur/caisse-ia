<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\UnknownItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Afficher la page d'accueil des paramètres
     */
    public function index(Request $request)
    {
        // Vérifier si la confirmation de mot de passe est nécessaire
        if (!$request->session()->has('settings_password_confirmed')) {
            return view('panel.settings.password-confirmation');
        }

        // Statistiques pour le dashboard des paramètres
        $stats = [
            'total_articles' => Article::count(),
            'zero_stock_articles' => Article::whereDoesntHave('variants.stocks', function ($query) {
                $query->where('quantity', '>', 0);
            })->orWhereHas('variants', function ($query) {
                $query->whereDoesntHave('stocks');
            })->count(),
            'unknown_items' => UnknownItem::count(),
            'total_categories' => Category::count(),
            'total_attributes' => Attribute::count(),
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
        ];

        return view('panel.settings.index', compact('stats'));
    }

    /**
     * Confirmer le mot de passe pour accéder aux paramètres
     */
    public function confirmPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
        ]);

        if (Hash::check($request->password, Auth::user()->password)) {
            $request->session()->put('settings_password_confirmed', true);
            $request->session()->put('settings_last_activity', time());
            return redirect()->route('settings.index');
        }

        return back()->withErrors([
            'password' => 'Le mot de passe fourni ne correspond pas à votre mot de passe actuel.',
        ]);
    }

    /**
     * Réinitialiser la confirmation de mot de passe
     */
    public function resetPasswordConfirmation(Request $request)
    {
        $request->session()->forget(['settings_password_confirmed', 'settings_last_activity']);
        return redirect()->route('settings.index');
    }
}
