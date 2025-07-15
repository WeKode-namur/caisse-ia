<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Log;

class AttributesController extends Controller
{
    public function __construct()
    {
        // Vérification des permissions dans chaque méthode
    }

    /**
     * Affiche la liste des attributs
     */
    public function index(Request $request)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        // Si c'est une requête AJAX, retourner le tableau
        if ($request->ajax()) {
            return $this->getTableData($request);
        }

        // Sinon, afficher la page complète
        return view('panel.settings.attributes.index');
    }

    /**
     * Retourne les données du tableau en AJAX
     */
    public function getTableData(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type', '');
        $status = $request->get('status', 'active'); // Par défaut 'active'
        $sort = $request->get('sort', 'name');
        $page = $request->get('page', 1);

        $query = Attribute::withCount(['values', 'activeValues'])
            ->withCount(['values as total_values_count', 'activeValues as active_values_count']);

        // Filtre par recherche (nom)
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Filtre par type
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        // Filtre par statut
        if ($status !== 'all') {
            if ($status === 'active') {
                $query->where('actif', true);
            } elseif ($status === 'inactive') {
                $query->where('actif', false);
            }
        }

        // Tri
        switch ($sort) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'type':
                $query->orderBy('type')->orderBy('name');
                break;
            case 'created_at':
                $query->orderBy('created_at', 'desc');
                break;
            case 'values_count':
                $query->orderBy('total_values_count', 'desc');
                break;
            default:
                $query->orderBy('actif', 'desc')->orderBy('name'); // Actifs en premier
                break;
        }

        // Pagination
        $perPage = 15;
        $attributes = $query->paginate($perPage);

        // Charger les compteurs d'articles et variants pour chaque attribut
        foreach ($attributes as $attribute) {
            $attribute->articles_count = $attribute->getArticlesCountAttribute();
            $attribute->variants_count = $attribute->getVariantsCountAttribute();
        }

        return view('panel.settings.attributes.partials.table', compact('attributes', 'search', 'type', 'status', 'sort'));
    }

    /**
     * Retourne les statistiques en AJAX
     */
    public function getStats(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type', '');
        $status = $request->get('status', 'all');

        $query = Attribute::query();

        // Appliquer les mêmes filtres que pour le tableau
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        if ($status !== 'all') {
            if ($status === 'active') {
                $query->where('actif', true);
            } elseif ($status === 'inactive') {
                $query->where('actif', false);
            }
        }

        $stats = [
            'total' => $query->count(),
            'active' => (clone $query)->where('actif', true)->count(),
            'inactive' => (clone $query)->where('actif', false)->count(),
            'by_type' => [
                'number' => (clone $query)->where('type', 'number')->count(),
                'select' => (clone $query)->where('type', 'select')->count(),
                'color' => (clone $query)->where('type', 'color')->count(),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Vérifie les permissions d'administrateur
     */
    private function checkAdminPermissions()
    {
        if (Auth::user()->is_admin < 80) {
            return redirect()->route('settings.index')->with('error', 'Accès refusé. Niveau d\'administrateur insuffisant.');
        }
        return null;
    }

    /**
     * Enregistre un nouvel attribut
     */
    public function store(Request $request)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:attributes,name',
            'type' => 'required|in:number,select,color',
            'unit' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Attribute::create($request->all());

        return redirect()->route('settings.attributes.index')
            ->with('success', 'Attribut créé avec succès.');
    }

    /**
     * Affiche le formulaire de création d'un attribut
     */
    public function create()
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        return view('panel.settings.attributes.create');
    }

    /**
     * Affiche le formulaire d'édition d'un attribut
     */
    public function edit(Attribute $attribute)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        return view('panel.settings.attributes.edit', compact('attribute'));
    }

    /**
     * Supprime un attribut (désactive au lieu de supprimer)
     */
    public function destroy(Attribute $attribute)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        // Compter les articles et variants liés
        $articlesCount = $attribute->articles_count;
        $variantsCount = $attribute->variants_count;

        // Désactiver l'attribut au lieu de le supprimer
        $attribute->deactivate();

        $message = 'Attribut désactivé avec succès.';
        if ($articlesCount > 0 || $variantsCount > 0) {
            $message = "Attribut désactivé avec succès. {$articlesCount} articles et {$variantsCount} variants sont encore liés à cet attribut.";
        }

        return redirect()->route('settings.attributes.index')
            ->with('success', $message);
    }

    /**
     * Affiche les valeurs d'un attribut
     */
    public function values(Attribute $attribute)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        $values = $attribute->values()->orderBy('order')->orderBy('value')->get();
        $inactiveValues = $attribute->values()->inactive()->orderBy('order')->orderBy('value')->get();

        // Charger les compteurs d'articles et variants pour chaque valeur
        foreach ($values as $value) {
            $value->articles_count = $value->getArticlesCountAttribute();
            $value->variants_count = $value->getVariantsCountAttribute();
        }

        foreach ($inactiveValues as $value) {
            $value->articles_count = $value->getArticlesCountAttribute();
            $value->variants_count = $value->getVariantsCountAttribute();
        }

        return view('panel.settings.attributes.values', compact('attribute', 'values', 'inactiveValues'));
    }

    /**
     * Enregistre une nouvelle valeur pour un attribut
     */
    public function storeValue(Request $request, Attribute $attribute)
    {
        Log::debug('storeValue appelé', [
            'user_id' => auth()->id(),
            'attribute_id' => $attribute->id,
            'is_ajax' => $request->ajax(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'data' => $request->all(),
        ]);

        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission refusée.'
                ], 403);
            }
            return $permissionCheck;
        }

        // Règles de validation selon le type d'attribut
        $valueRules = ['required', 'max:255'];
        if ($attribute->type === 'number') {
            $valueRules[] = 'numeric';
        } else {
            $valueRules[] = 'string';
        }

        $validator = Validator::make($request->all(), [
            'value' => $valueRules,
            'second_value' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier si la valeur existe déjà
        if ($attribute->values()->where('value', $request->value)->exists()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cette valeur existe déjà pour cet attribut.'], 409);
            }
            return redirect()->back()
                ->with('error', 'Cette valeur existe déjà pour cet attribut.')
                ->withInput();
        }

        // Déterminer l'ordre automatiquement (dernière position + 1)
        $maxOrder = $attribute->values()->max('order') ?? 0;
        $order = $maxOrder + 1;

        $attribute->values()->create([
            'value' => $request->value,
            'second_value' => $request->second_value,
            'order' => $order
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            Log::debug('storeValue - retour JSON succès');
            return response()->json(['success' => true, 'message' => 'Valeur ajoutée avec succès.']);
        }

        return redirect()->route('settings.attributes.values', $attribute)
            ->with('success', 'Valeur ajoutée avec succès.');
    }

    /**
     * Met à jour une valeur d'attribut
     */
    public function updateValue(Request $request, Attribute $attribute, AttributeValue $value)
    {
        Log::debug('updateValue appelé', [
            'user_id' => auth()->id(),
            'attribute_id' => $attribute->id,
            'value_id' => $value->id,
            'is_ajax' => $request->ajax(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'data' => $request->all(),
        ]);

        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission refusée.'
                ], 403);
            }
            return $permissionCheck;
        }

        // Règles de validation selon le type d'attribut
        $valueRules = ['required', 'max:255'];
        if ($attribute->type === 'number') {
            $valueRules[] = 'numeric';
        } else {
            $valueRules[] = 'string';
        }

        $validator = Validator::make($request->all(), [
            'value' => $valueRules,
            'second_value' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::debug('updateValue - validation échouée', [
                'errors' => $validator->errors()->toArray(),
                'data' => $request->all()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier si la valeur existe déjà (sauf pour cette valeur)
        if ($attribute->values()->where('value', $request->value)->where('id', '!=', $value->id)->exists()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cette valeur existe déjà pour cet attribut.'], 409);
            }
            return redirect()->back()
                ->with('error', 'Cette valeur existe déjà pour cet attribut.')
                ->withInput();
        }

        $value->update([
            'value' => $request->value,
            'second_value' => $request->second_value
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Valeur mise à jour !']);
        }

        return redirect()->route('settings.attributes.values', $attribute)
            ->with('success', 'Valeur mise à jour avec succès.');
    }

    /**
     * Met à jour un attribut
     */
    public function update(Request $request, Attribute $attribute)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:attributes,name,' . $attribute->id,
            'type' => 'required|in:number,select,color',
            'unit' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $attribute->update($request->all());

        return redirect()->route('settings.attributes.index')
            ->with('success', 'Attribut mis à jour avec succès.');
    }

    /**
     * Supprime une valeur d'attribut (désactive au lieu de supprimer)
     */
    public function destroyValue(Attribute $attribute, AttributeValue $value)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission refusée.'
                ], 403);
            }
            return $permissionCheck;
        }

        try {
            // Compter les articles et variants liés
            $articlesCount = $value->getArticlesCountAttribute();
            $variantsCount = $value->getVariantsCountAttribute();

            // Désactiver la valeur au lieu de la supprimer
            $value->deactivate();

            $message = 'Valeur désactivée avec succès.';
            if ($articlesCount > 0 || $variantsCount > 0) {
                $message = "Valeur désactivée avec succès. {$articlesCount} articles et {$variantsCount} variants sont encore liés à cette valeur.";
            }

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('settings.attributes.values', $attribute)
                ->with('success', $message);
        } catch (Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la désactivation : ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('settings.attributes.values', $attribute)
                ->with('error', 'Erreur lors de la désactivation : ' . $e->getMessage());
        }
    }

    public function updateValuesOrder(Request $request, Attribute $attribute)
    {
        $order = $request->input('order');
        foreach ($order as $index => $id) {
            AttributeValue::where('id', $id)->where('attribute_id', $attribute->id)
                ->update(['order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }

    /**
     * Affiche les informations d'une valeur d'attribut (AJAX)
     */
    public function showValue(Attribute $attribute, AttributeValue $value)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        // Vérifier que la valeur appartient bien à l'attribut
        if ($value->attribute_id !== $attribute->id) {
            return response()->json(['success' => false, 'message' => 'Valeur non trouvée'], 404);
        }

        // Compter les variants liés
        $variantsCount = $value->variants()->count();

        // Compter les articles liés (via les variants)
        $articlesCount = $value->variants()
            ->join('articles', 'variants.article_id', '=', 'articles.id')
            ->distinct('articles.id')
            ->count('articles.id');

        return response()->json([
            'success' => true,
            'value' => [
                'id' => $value->id,
                'value' => $value->value,
                'second_value' => $value->second_value,
                'order' => $value->order,
                'variants_count' => $variantsCount,
                'articles_count' => $articlesCount,
                'created_at_formatted' => $value->created_at->format('d/m/Y à H:i'),
                'created_at' => $value->created_at->toISOString(),
            ]
        ]);
    }

    /**
     * Retourne le tableau des valeurs d'attributs en AJAX
     */
    public function ajaxTable(Attribute $attribute)
    {
        $values = $attribute->values()->active()->orderBy('order')->orderBy('value')->get();

        // Charger les compteurs d'articles et variants pour chaque valeur
        foreach ($values as $value) {
            $value->articles_count = $value->getArticlesCountAttribute();
            $value->variants_count = $value->getVariantsCountAttribute();
        }

        return view('panel.settings.attributes._table', compact('attribute', 'values'))->render();
    }

    /**
     * Retourne le tableau des valeurs d'attributs inactives en AJAX
     */
    public function ajaxArchivesTable(Attribute $attribute)
    {
        $inactiveValues = $attribute->values()->inactive()->orderBy('order')->orderBy('value')->get();

        // Charger les compteurs d'articles et variants pour chaque valeur
        foreach ($inactiveValues as $value) {
            $value->articles_count = $value->getArticlesCountAttribute();
            $value->variants_count = $value->getVariantsCountAttribute();
        }

        return view('panel.settings.attributes._archives_table', compact('attribute', 'inactiveValues'))->render();
    }

    /**
     * Réactive un attribut
     */
    public function activate(Attribute $attribute)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        $attribute->activate();

        return redirect()->route('settings.attributes.index')
            ->with('success', 'Attribut réactivé avec succès.');
    }

    /**
     * Réactive une valeur d'attribut
     */
    public function activateValue(Attribute $attribute, AttributeValue $value)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        $value->activate();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Valeur réactivée avec succès.'
            ]);
        }

        return redirect()->route('settings.attributes.values', $attribute)
            ->with('success', 'Valeur réactivée avec succès.');
    }
}
