<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Attribute;
use App\Models\Category;
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
        ]);

        if (Hash::check($request->password, Auth::user()->password)) {
            $request->session()->put('settings_password_confirmed', true);
            return redirect()->route('settings.index')->with('success', 'Accès aux paramètres autorisé.');
        }

        return back()->withErrors(['password' => 'Le mot de passe est incorrect.']);
    }

    /**
     * Afficher les articles avec stock zéro
     */
    public function zeroStock()
    {
        $articles = Article::whereDoesntHave('variants.stocks', function ($query) {
            $query->where('quantity', '>', 0);
        })
            ->orWhereHas('variants', function ($query) {
                $query->whereDoesntHave('stocks');
            })
            ->with(['category', 'variants.stocks'])
            ->orderBy('name')
            ->paginate(20);

        return view('panel.settings.zero-stock.index', compact('articles'));
    }

    /**
     * Mise à jour en masse des articles avec stock zéro
     */
    public function bulkUpdateZeroStock(Request $request)
    {

        $request->validate([
            'action' => 'required|in:delete,archive,update_stock',
            'article_ids' => 'required|array',
            'article_ids.*' => 'exists:articles,id',
        ]);

        $articles = Article::whereIn('id', $request->article_ids)
            ->where(function ($query) {
                $query->whereDoesntHave('variants.stocks', function ($stockQuery) {
                    $stockQuery->where('quantity', '>', 0);
                })
                    ->orWhereHas('variants', function ($variantQuery) {
                        $variantQuery->whereDoesntHave('stocks');
                    });
            });

        switch ($request->action) {
            case 'delete':
                $articles->delete();
                $message = 'Articles supprimés avec succès.';
                break;
            case 'archive':
                $articles->update(['status' => 'archived']);
                $message = 'Articles archivés avec succès.';
                break;
            case 'update_stock':
                $request->validate([
                    'new_stock' => 'required|integer|min:0',
                ]);

                // Pour chaque article, créer un stock pour chaque variant
                $articles->with('variants')->get()->each(function ($article) use ($request) {
                    foreach ($article->variants as $variant) {
                        // Créer ou mettre à jour le stock pour ce variant
                        $variant->stocks()->updateOrCreate(
                            ['variant_id' => $variant->id],
                            [
                                'quantity' => $request->new_stock,
                                'buy_price' => $variant->buy_price ?? $article->buy_price ?? 0,
                                'lot_reference' => 'STOCK-ZERO-UPDATE-' . now()->format('Y-m-d')
                            ]
                        );
                    }
                });
                $message = 'Stock mis à jour avec succès.';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Réinitialiser la confirmation de mot de passe
     */
    public function resetPasswordConfirmation(Request $request)
    {
        $request->session()->forget('settings_password_confirmed');
        return redirect()->route('settings.index');
    }
}
