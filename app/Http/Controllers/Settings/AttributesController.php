<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AttributesController extends Controller
{
    public function __construct()
    {
        // Vérification des permissions dans chaque méthode
    }

    /**
     * Affiche la liste des attributs
     */
    public function index()
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        $attributes = Attribute::withCount('values')->orderBy('name')->get();

        return view('panel.settings.attributes.index', compact('attributes'));
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
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:text,number,select,boolean,date',
            'is_required' => 'boolean',
            'is_searchable' => 'boolean',
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
     * Supprime un attribut
     */
    public function destroy(Attribute $attribute)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        // Vérifier si l'attribut est utilisé
        if ($attribute->values()->count() > 0) {
            return redirect()->route('settings.attributes.index')
                ->with('error', 'Impossible de supprimer cet attribut car il possède des valeurs associées.');
        }

        $attribute->delete();

        return redirect()->route('settings.attributes.index')
            ->with('success', 'Attribut supprimé avec succès.');
    }

    /**
     * Affiche les valeurs d'un attribut
     */
    public function values(Attribute $attribute)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        $values = $attribute->values()->orderBy('order')->orderBy('value')->get();

        return view('panel.settings.attributes.values', compact('attribute', 'values'));
    }

    /**
     * Enregistre une nouvelle valeur pour un attribut
     */
    public function storeValue(Request $request, Attribute $attribute)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        $validator = Validator::make($request->all(), [
            'value' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier si la valeur existe déjà
        if ($attribute->values()->where('value', $request->value)->exists()) {
            return redirect()->back()
                ->with('error', 'Cette valeur existe déjà pour cet attribut.')
                ->withInput();
        }

        // Déterminer l'ordre automatiquement (dernière position + 1)
        $maxOrder = $attribute->values()->max('order') ?? 0;
        $order = $maxOrder + 1;

        $attribute->values()->create([
            'value' => $request->value,
            'description' => $request->description,
            'order' => $order
        ]);

        return redirect()->route('settings.attributes.values', $attribute)
            ->with('success', 'Valeur ajoutée avec succès.');
    }

    /**
     * Met à jour une valeur d'attribut
     */
    public function updateValue(Request $request, Attribute $attribute, AttributeValue $value)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        $validator = Validator::make($request->all(), [
            'value' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
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
            'description' => $request->description
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
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:text,number,select,boolean,date',
            'is_required' => 'boolean',
            'is_searchable' => 'boolean',
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
     * Supprime une valeur d'attribut
     */
    public function destroyValue(Attribute $attribute, AttributeValue $value)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        // Vérifier si la valeur est utilisée dans des articles
        if ($value->articles()->count() > 0) {
            return redirect()->route('settings.attributes.values', $attribute)
                ->with('error', 'Impossible de supprimer cette valeur car elle est utilisée par des articles.');
        }

        $value->delete();

        return redirect()->route('settings.attributes.values', $attribute)
            ->with('success', 'Valeur supprimée avec succès.');
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
                'description' => $value->description,
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
        $values = $attribute->values()->orderBy('order')->orderBy('value')->get();
        return view('panel.settings.attributes._table', compact('attribute', 'values'))->render();
    }
}
