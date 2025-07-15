<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Variant;
use Exception;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    /**
     * Display a listing of variants for an article
     */
    public function index(Article $article)
    {
        $variants = $article->variants()
            ->with(['attributeValues.attribute', 'stocks', 'medias'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('panel.variants.index', compact('article', 'variants'));
    }

    /**
     * Show the form for creating a new variant
     */
    public function create(Article $article)
    {
        $attributes = Attribute::active()->with('activeValues')->orderBy('name')->get();

        return view('panel.variants.create', compact('article', 'attributes'));
    }

    /**
     * Store a newly created variant
     */
    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            'barcode' => 'nullable|string|unique:variants,barcode',
            'reference' => 'nullable|string|max:255',
            'sell_price' => 'nullable|numeric|min:0',
            'buy_price' => 'nullable|numeric|min:0',
            'attribute_values' => 'nullable|array',
            'attribute_values.*' => 'exists:attribute_values,id',
            'stock_quantity' => 'nullable|integer|min:0',
            'stock_price' => 'nullable|numeric|min:0',
            'lot_reference' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date'
        ]);

        try {
            // Créer le variant
            $variant = $article->variants()->create([
                'barcode' => $validated['barcode'] ?? null,
                'reference' => $validated['reference'] ?? null,
                'sell_price' => $validated['sell_price'] ?? null,
                'buy_price' => $validated['buy_price'] ?? null,
            ]);

            // Associer les attributs
            if (isset($validated['attribute_values'])) {
                $variant->attributeValues()->attach($validated['attribute_values']);
            }

            // Créer le stock initial si fourni
            if (isset($validated['stock_quantity']) && $validated['stock_quantity'] > 0) {
                $variant->stocks()->create([
                    'buy_price' => $validated['stock_price'] ?? $article->buy_price ?? 0,
                    'quantity' => $validated['stock_quantity'],
                    'lot_reference' => $validated['lot_reference'] ?? null,
                    'expiry_date' => $validated['expiry_date'] ?? null,
                ]);
            }

            return redirect()
                ->route('articles.variants.show', [$article, $variant])
                ->with('success', 'Variant créé avec succès');

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified variant
     */
    public function show(Article $article, Variant $variant)
    {
        $variant->load([
            'attributeValues.attribute',
            'stocks' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'medias'
        ]);

        // Récupérer l'historique des mouvements de stock (à implémenter plus tard)
        $stockMovements = collect(); // Placeholder pour l'historique

        return view('panel.variants.show', compact('article', 'variant', 'stockMovements'));
    }

    /**
     * Show the form for editing the specified variant
     */
    public function edit(Article $article, Variant $variant)
    {
        $variant->load('attributeValues');
        $attributes = Attribute::with('attributeValues')->orderBy('name')->get();

        return view('panel.variants.edit', compact('article', 'variant', 'attributes'));
    }

    /**
     * Update the specified variant
     */
    public function update(Request $request, Article $article, Variant $variant)
    {
        $validated = $request->validate([
            'barcode' => [
                'nullable',
                'string',
                'unique:variants,barcode,' . $variant->id
            ],
            'reference' => 'nullable|string|max:255',
            'sell_price' => 'nullable|numeric|min:0',
            'buy_price' => 'nullable|numeric|min:0',
            'attribute_values' => 'nullable|array',
            'attribute_values.*' => 'exists:attribute_values,id'
        ]);

        try {
            // Mettre à jour le variant
            $variant->update([
                'barcode' => $validated['barcode'] ?? null,
                'reference' => $validated['reference'] ?? null,
                'sell_price' => $validated['sell_price'] ?? null,
                'buy_price' => $validated['buy_price'] ?? null,
            ]);

            // Mettre à jour les attributs
            if (isset($validated['attribute_values'])) {
                $variant->attributeValues()->sync($validated['attribute_values']);
            } else {
                $variant->attributeValues()->detach();
            }

            return redirect()
                ->route('articles.variants.show', [$article, $variant])
                ->with('success', 'Variant mis à jour avec succès');

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified variant
     */
    public function destroy(Article $article, Variant $variant)
    {
        try {
            // Vérifier s'il y a du stock
            $hasStock = $variant->stocks()->where('quantity', '>', 0)->exists();

            if ($hasStock) {
                return redirect()
                    ->back()
                    ->with('error', 'Impossible de supprimer un variant qui a du stock');
            }

            // Vérifier si c'est le dernier variant de l'article
            if ($article->variants()->count() <= 1) {
                return redirect()
                    ->back()
                    ->with('error', 'Impossible de supprimer le dernier variant d\'un article');
            }

            $variant->delete();

            return redirect()
                ->route('articles.variants.index', $article)
                ->with('success', 'Variant supprimé avec succès');

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate a variant
     */
    public function duplicate(Article $article, Variant $variant)
    {
        try {
            // Dupliquer le variant
            $newVariant = $variant->replicate();
            $newVariant->barcode = null; // Réinitialiser le code-barres
            $newVariant->reference = $variant->reference ? $variant->reference . ' (Copie)' : null;
            $newVariant->save();

            // Dupliquer les relations attributs
            $newVariant->attributeValues()->attach(
                $variant->attributeValues->pluck('id')->toArray()
            );

            return redirect()
                ->route('articles.variants.show', [$article, $newVariant])
                ->with('success', 'Variant dupliqué avec succès');

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la duplication: ' . $e->getMessage());
        }
    }

    /**
     * Generate combinations of variants based on attributes
     */
    public function generateCombinations(Request $request, Article $article)
    {
        $validated = $request->validate([
            'attributes' => 'required|array|min:1',
            'attributes.*' => 'exists:attributes,id'
        ]);

        try {
            $attributes = Attribute::with('attributeValues')
                ->whereIn('id', $validated['attributes'])
                ->get();

            // Générer toutes les combinaisons possibles
            $combinations = $this->generateAttributeCombinations($attributes);

            $createdVariants = 0;
            foreach ($combinations as $combination) {
                // Vérifier si cette combinaison existe déjà
                $exists = $article->variants()
                    ->whereHas('attributeValues', function ($query) use ($combination) {
                        $query->whereIn('attribute_value_id', $combination);
                    }, '=', count($combination))
                    ->exists();

                if (!$exists) {
                    // Créer le variant
                    $variant = $article->variants()->create([
                        'reference' => $this->generateVariantReference($article, $combination)
                    ]);

                    // Associer les attributs
                    $variant->attributeValues()->attach($combination);
                    $createdVariants++;
                }
            }

            return redirect()
                ->route('articles.variants.index', $article)
                ->with('success', "$createdVariants variants créés avec succès");

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la génération: ' . $e->getMessage());
        }
    }

    /**
     * Generate all possible combinations of attribute values
     */
    private function generateAttributeCombinations($attributes)
    {
        $combinations = [[]];

        foreach ($attributes as $attribute) {
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($attribute->attributeValues as $value) {
                    $newCombinations[] = array_merge($combination, [$value->id]);
                }
            }
            $combinations = $newCombinations;
        }

        return $combinations;
    }

    /**
     * Generate a reference for a variant based on its attributes
     */
    private function generateVariantReference($article, $attributeValueIds)
    {
        $values = AttributeValue::whereIn('id', $attributeValueIds)
            ->with('attribute')
            ->get();

        $parts = [$article->id];
        foreach ($values as $value) {
            $parts[] = strtoupper(substr($value->value, 0, 3));
        }

        return implode('-', $parts);
    }
}
