<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Attribute, AttributeValue, Subtype, Type};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class InventoryApiController extends Controller
{
    /**
     * Retourner les types d'une catégorie
     */
    public function getTypes($categoryId)
    {
        $types = Type::where('category_id', $categoryId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($types);
    }

    /**
     * Retourner les sous-types d'un type
     */
    public function getSubtypes($typeId)
    {
        $subtypes = Subtype::where('type_id', $typeId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($subtypes);
    }

    /**
     * Obtenir tous les attributs disponibles
     */
    public function getAttributes(): JsonResponse
    {
        try {
            $attributes = Attribute::select('id', 'name', 'type', 'unit')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'attributes' => $attributes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des attributs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les valeurs d'un attribut
     */
    public function getAttributeValues($id): JsonResponse
    {
        try {
            $attribute = Attribute::findOrFail($id);

            $values = AttributeValue::where('attribute_id', $id)
                ->select('id', 'value', 'second_value', 'order')
                ->orderBy('order')
                ->orderBy('value')
                ->get();

            return response()->json([
                'success' => true,
                'attribute' => $attribute,
                'values' => $values
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des valeurs d\'attribut',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechercher des attributs par nom
     */
    public function searchAttributes(Request $request)
    {
        $query = $request->get('q', '');

        $attributes = Attribute::where('name', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'type', 'unit']);

        return response()->json($attributes);
    }

    /**
     * Créer rapidement un attribut et sa valeur
     */
    public function quickCreateAttribute(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,select,color',
            'unit' => 'nullable|string|max:50',
            'value' => 'required|string|max:255',
            'second_value' => 'nullable|string|max:255'
        ]);

        try {
            \DB::beginTransaction();

            // Créer l'attribut s'il n'existe pas
            $attribute = Attribute::firstOrCreate(
                ['name' => $validated['name']],
                [
                    'type' => $validated['type'],
                    'unit' => $validated['unit']
                ]
            );

            // Créer la valeur
            $attributeValue = AttributeValue::firstOrCreate([
                'attribute_id' => $attribute->id,
                'value' => $validated['value'],
                'second_value' => $validated['second_value']
            ]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'attribute' => $attribute,
                'attribute_value' => $attributeValue
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 422);
        }
    }
}
