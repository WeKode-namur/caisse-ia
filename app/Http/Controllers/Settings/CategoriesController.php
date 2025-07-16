<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Type;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function index()
    {
        return view('panel.settings.categories.index');
    }

    public function edit(Category $category)
    {
        return view('panel.settings.categories.edit', compact('category'));
    }

    public function getTable(Request $request)
    {
        $query = Category::query();

        // Filtre par statut
        if ($request->has('status') && $request->status !== 'all') {
            switch ($request->status) {
                case 'active':
                    $query->where('actif', true);
                    break;
                case 'inactive':
                    $query->where('actif', false);
                    break;
            }
        }
        // Si status = 'all', on ne filtre rien (affiche tout)

        $categories = $query->orderBy('name')->get();

        return view('panel.settings.categories.partials._table', compact('categories'));
    }

    public function getStats()
    {
        $totalCategories = Category::count();
        $activeCategories = Category::where('actif', true)->count();
        $inactiveCategories = Category::where('actif', false)->count();

        return view('panel.settings.categories.partials._stats', compact('totalCategories', 'activeCategories', 'inactiveCategories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.unique' => 'Cette catégorie existe déjà.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
            'icon.max' => 'L\'icône ne peut pas dépasser 100 caractères.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'icon' => $request->icon,
                'actif' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie créée avec succès.',
                'category' => $category
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la catégorie.'
            ], 500);
        }
    }

    public function create()
    {
        return view('panel.settings.categories.create');
    }

    public function toggle(Category $category)
    {
        try {
            $category->update(['actif' => !$category->actif]);

            $message = $category->actif ? 'Catégorie activée avec succès.' : 'Catégorie désactivée avec succès.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'actif' => $category->actif
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du statut.'
            ], 500);
        }
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.unique' => 'Cette catégorie existe déjà.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
            'icon.max' => 'L\'icône ne peut pas dépasser 100 caractères.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'icon' => $request->icon,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie modifiée avec succès.',
                'category' => $category
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification de la catégorie.'
            ], 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            // Vérifier s'il y a des types liés
            $typesCount = Type::where('category_id', $category->id)->count();

            if ($typesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer cette catégorie car elle contient ' . $typesCount . ' type(s).'
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Catégorie supprimée avec succès.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la catégorie.'
            ], 500);
        }
    }

    public function getIcons()
    {
        $iconsPath = public_path('data/fontawesome-icons.json');

        if (!File::exists($iconsPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Fichier d\'icônes non trouvé.'
            ], 404);
        }

        try {
            $icons = json_decode(File::get($iconsPath), true);

            // Grouper par catégorie
            $groupedIcons = [];
            foreach ($icons as $icon) {
                $category = $icon['category'] ?? 'Autres';
                if (!isset($groupedIcons[$category])) {
                    $groupedIcons[$category] = [];
                }
                $groupedIcons[$category][] = $icon;
            }

            return response()->json([
                'success' => true,
                'icons' => $groupedIcons
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des icônes.'
            ], 500);
        }
    }

    // Méthodes pour les types
    public function types(Category $category)
    {
        return view('panel.settings.categories.types', compact('category'));
    }

    public function getTypesTable(Request $request, Category $category)
    {
        $query = Type::where('category_id', $category->id);

        // Filtre par statut
        if ($request->has('status') && $request->status !== 'all') {
            switch ($request->status) {
                case 'active':
                    $query->where('actif', true);
                    break;
                case 'inactive':
                    $query->where('actif', false);
                    break;
            }
        }
        // Si status = 'all', on ne filtre rien (affiche tout)

        $types = $query->orderBy('name')->get();

        return view('panel.settings.categories.partials._types_table', compact('types'));
    }

    public function getTypesStats(Category $category)
    {
        $totalTypes = Type::where('category_id', $category->id)->count();
        $activeTypes = Type::where('category_id', $category->id)->where('actif', true)->count();
        $inactiveTypes = Type::where('category_id', $category->id)->where('actif', false)->count();

        return view('panel.settings.categories.partials._types_stats', compact('totalTypes', 'activeTypes', 'inactiveTypes'));
    }

    public function storeType(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:types,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Le nom du type est obligatoire.',
            'name.unique' => 'Ce type existe déjà.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $type = Type::create([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $category->id,
                'actif' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Type ajouté avec succès.',
                'type' => $type
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du type.'
            ], 500);
        }
    }

    public function updateType(Request $request, Category $category, Type $type)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:types,name,' . $type->id,
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Le nom du type est obligatoire.',
            'name.unique' => 'Ce type existe déjà.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $type->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Type modifié avec succès.',
                'type' => $type
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du type.'
            ], 500);
        }
    }

    public function toggleType(Category $category, Type $type)
    {
        try {
            $type->update(['actif' => !$type->actif]);

            $message = $type->actif ? 'Type activé avec succès.' : 'Type désactivé avec succès.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'actif' => $type->actif
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du statut.'
            ], 500);
        }
    }

    public function destroyType(Category $category, Type $type)
    {
        try {
            // Vérifier s'il y a des articles liés
            $articlesCount = $type->articles()->count();

            if ($articlesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer ce type car il est utilisé par ' . $articlesCount . ' article(s).'
                ], 422);
            }

            $type->delete();

            return response()->json([
                'success' => true,
                'message' => 'Type supprimé avec succès.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du type.'
            ], 500);
        }
    }
}
