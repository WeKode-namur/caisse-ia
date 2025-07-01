<?php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Variant;
use App\Services\VariantService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CreationController extends Controller
{
    public function stepOne($draftId = null)
    {
        $draft = null;
        if ($draftId) {
            $draft = Article::where('status', Article::STATUS_DRAFT)->findOrFail($draftId);
        }

        $categories = Category::with(['types.subtypes'])->orderBy('name')->get();
        $tvaRates = [6, 12, 21]; // Taux TVA belges

        $formAction = $draftId
            ? route('inventory.create.step.one.store')
            : route('inventory.create.step.one.store');

        return view('panel.inventory.create.step-one', compact('draft', 'categories', 'tvaRates', 'formAction', 'draftId'));
    }

    public function storeStepOne(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'tva' => 'required|integer|in:6,12,21',
            'buy_price' => 'nullable|numeric|min:0',
            'sell_price' => 'nullable|numeric|min:0',
        ], [
            // Messages pour le champ 'name'
            'name.required' => 'Le nom de l\'article est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',

            // Messages pour le champ 'category_id'
            'category_id.required' => 'Veuillez sélectionner une catégorie.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',

            // Messages pour le champ 'tva'
            'tva.required' => 'Le taux de TVA est obligatoire.',
            'tva.integer' => 'Le taux de TVA doit être un nombre entier.',
            'tva.in' => 'Le taux de TVA doit être 6%, 12% ou 21%.',

            // Messages pour les prix
            'buy_price.numeric' => 'Le prix d\'achat doit être un nombre.',
            'buy_price.min' => 'Le prix d\'achat ne peut pas être négatif.',
            'sell_price.numeric' => 'Le prix de vente doit être un nombre.',
            'sell_price.min' => 'Le prix de vente ne peut pas être négatif.',

            // Messages pour la description
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',

            // Messages pour type et subtype
            'type_id.exists' => 'Le type sélectionné n\'existe pas.',
            'subtype_id.exists' => 'Le sous-type sélectionné n\'existe pas.',
        ]);

        $data = $request->only(['name', 'description', 'category_id', 'type_id', 'subtype_id', 'reference', 'tva']);

        // Ajouter les prix seulement si la checkbox est cochée
        if ($request->has('prix_unique')) {
            $data['buy_price'] = $request->buy_price;
            $data['sell_price'] = $request->sell_price;
        }

        $data['status'] = Article::STATUS_DRAFT;

        if ($request->draft_id) {
            $article = Article::findOrFail($request->draft_id);
            $article->update($data);
        } else {
            $article = Article::create($data);
        }

        if ($request->action === 'save_exit') {
            return redirect()->route('inventory.create.index')
                ->with('success', 'Brouillon sauvegardé avec succès');
        }

        return redirect()->route('inventory.create.step.two', $article->id);
    }
    /**
     * Étape 2 : Gestion des variants et stock
     */
    public function stepTwo($draftId)
    {
        $draft = Article::where('id', $draftId)
            ->where('status', 'draft')
            ->with(['category', 'type', 'subtype', 'variants.stocks', 'variants.attributeValues.attribute'])
            ->firstOrFail();

        // Charger les attributs disponibles pour les variants
        $attributes = Attribute::orderBy('name')->get();

        // Préparer les données des variants existants
        $existingVariants = $draft->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'barcode' => $variant->barcode,
                'reference' => $variant->reference,
                'sell_price' => $variant->sell_price,
                'buy_price' => $variant->buy_price,
                'attributes' => $variant->attributeValues->map(function ($av) {
                    return [
                        'attribute_id' => $av->attribute_id,
                        'attribute_name' => $av->attribute->name,
                        'value_id' => $av->id,
                        'value' => $av->value,
                        'second_value' => $av->second_value
                    ];
                }),
                'stock' => $variant->stocks->first() ? [
                    'id' => $variant->stocks->first()->id,
                    'quantity' => $variant->stocks->first()->quantity,
                    'buy_price' => $variant->stocks->first()->buy_price,
                    'lot_reference' => $variant->stocks->first()->lot_reference,
                    'expiry_date' => $variant->stocks->first()->expiry_date?->format('Y-m-d')
                ] : null
            ];
        });

        $formAction = route('inventory.create.step.two.store', $draftId);

        return view('panel.inventory.create.step-two', compact(
            'draft',
            'draftId',
            'attributes',
            'existingVariants',
            'formAction'
        ));
    }

    /**
     * Sauvegarde des variants et finalisation
     */
    public function storeStepTwo(Request $request, $draftId)
    {
        $draft = Article::where('id', $draftId)
            ->where('status', 'draft')
            ->firstOrFail();

        $action = $request->input('action');

        try {
            DB::beginTransaction();

            switch ($action) {
                case 'save_exit':
                    // Sauvegarder et quitter sans finaliser
                    break;

                case 'finish':
                    // Finaliser l'article (passer de draft à active)
                    $this->finalizeArticle($draft);
                    break;
            }

            DB::commit();

            if ($action === 'finish') {
                return redirect()->route('inventory.index')
                    ->with('success', 'Article créé avec succès !');
            }

            return redirect()->route('inventory.create.index')
                ->with('success', 'Brouillon sauvegardé.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('[storeStepTwo] Erreur lors de la sauvegarde', [
                'exception' => $e,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Erreur lors de la sauvegarde : ' . $e->getMessage()]);
        }
    }

    /**
     * Créer ou mettre à jour un variant via AJAX
     */
    public function storeVariant(Request $request, $draftId)
    {
        $draft = Article::where('id', $draftId)
            ->where('status', 'draft')
            ->firstOrFail();

        $validated = $request->validate([
            'barcode' => 'nullable|string|unique:variants,barcode,' . ($request->variant_id ?? 'null'),
            'reference' => 'nullable|string',
            'sell_price' => 'nullable|numeric|min:0',
            'buy_price' => 'nullable|numeric|min:0',
            'attributes' => 'required|array|min:1',
            'attributes.*.attribute_value_id' => 'required|exists:attribute_values,id',
            'stock.quantity' => 'nullable|integer|min:0',
            'stock.buy_price' => 'nullable|numeric|min:0',
            'stock.lot_reference' => 'nullable|string',
            'stock.expiry_date' => 'nullable|date',
            'images.*' => 'nullable|image|max:2048'
        ], [
            // Messages pour le code-barres
            'barcode.string' => 'Le code-barres doit être une chaîne de caractères.',
            'barcode.unique' => 'Ce code-barres est déjà utilisé par un autre variant.',

            // Messages pour la référence
            'reference.string' => 'La référence doit être une chaîne de caractères.',

            // Messages pour les prix
            'sell_price.numeric' => 'Le prix de vente doit être un nombre.',
            'sell_price.min' => 'Le prix de vente ne peut pas être négatif.',
            'buy_price.numeric' => 'Le prix d\'achat doit être un nombre.',
            'buy_price.min' => 'Le prix d\'achat ne peut pas être négatif.',

            // Messages pour les attributs
            'attributes.required' => 'Au moins un attribut est obligatoire pour créer un variant.',
            'attributes.array' => 'Les attributs doivent être fournis sous forme de liste.',
            'attributes.min' => 'Au moins un attribut doit être sélectionné.',
            'attributes.*.attribute_value_id.required' => 'Chaque attribut doit avoir une valeur sélectionnée.',
            'attributes.*.attribute_value_id.exists' => 'Une des valeurs d\'attribut sélectionnée n\'existe pas.',

            // Messages pour le stock
            'stock.quantity.integer' => 'La quantité en stock doit être un nombre entier.',
            'stock.quantity.min' => 'La quantité en stock ne peut pas être négative.',
            'stock.buy_price.numeric' => 'Le prix d\'achat du stock doit être un nombre.',
            'stock.buy_price.min' => 'Le prix d\'achat du stock ne peut pas être négatif.',
            'stock.lot_reference.string' => 'La référence du lot doit être une chaîne de caractères.',
            'stock.expiry_date.date' => 'La date d\'expiration doit être une date valide.',

            // Messages pour les images
            'images.*.image' => 'Chaque fichier uploadé doit être une image valide.',
            'images.*.max' => 'Chaque image ne peut pas dépasser 2 Mo.',
        ]);

        try {
            DB::beginTransaction();

            // Créer ou mettre à jour le variant
            $variant = Variant::updateOrCreate(
                ['id' => $request->variant_id],
                [
                    'article_id' => $draft->id,
                    'barcode' => $validated['barcode'],
                    'reference' => $validated['reference'] ?? null,
                    'sell_price' => $validated['sell_price'] ?? $draft->sell_price,
                    'buy_price' => $validated['buy_price'] ?? $draft->buy_price,
                ]
            );

            // Gestion des attributs
            $variant->attributeValues()->detach(); // Utiliser detach au lieu de delete

            $attributeValueIds = collect($validated['attributes'])->pluck('attribute_value_id');
            $variant->attributeValues()->attach($attributeValueIds);

            // Gestion du stock
            if (isset($validated['stock']) && $validated['stock']['quantity'] > 0) {
                Stock::updateOrCreate(
                    ['variant_id' => $variant->id],
                    [
                        'buy_price' => $validated['stock']['buy_price'] ?? $validated['buy_price'] ?? 0,
                        'quantity' => $validated['stock']['quantity'],
                        'lot_reference' => $validated['stock']['lot_reference'] ?? null,
                        'expiry_date' => isset($validated['stock']['expiry_date']) && $validated['stock']['expiry_date'] ?
                            Carbon::parse($validated['stock']['expiry_date']) : null,
                    ]
                );
            }

            // Gestion des images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('variants', 'public');
                    $variant->medias()->create([
                        'path' => $path,
                        'type' => 'image'
                    ]);
                }
            }

            DB::commit();

            // Retourner les données du variant créé
            $variant->load(['attributeValues.attribute', 'stocks', 'medias']);

            return response()->json([
                'success' => true,
                'message' => 'Variant sauvegardé avec succès',
                'variant' => $this->formatVariantForJson($variant)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde : ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Supprimer un variant via AJAX
     */
    public function deleteVariant($draftId, $variantId)
    {
        $draft = Article::where('id', $draftId)
            ->where('status', 'draft')
            ->firstOrFail();

        $variant = Variant::where('id', $variantId)
            ->where('article_id', $draft->id)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            // Supprimer tous les stocks liés
            $variant->stocks()->delete();
            // Supprimer tous les médias liés et les fichiers physiques
            foreach ($variant->medias as $media) {
                $media->deleteWithFile();
            }
            // Détacher tous les attributs
            $variant->attributeValues()->detach();
            // Supprimer le variant
            $variant->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Variant supprimé définitivement avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Charger les données d'un variant pour édition
     */
    public function getVariant($draftId, $variantId)
    {
        $draft = Article::where('id', $draftId)
            ->where('status', 'draft')
            ->firstOrFail();

        $variant = Variant::where('id', $variantId)
            ->where('article_id', $draft->id)
            ->with(['attributeValues.attribute', 'stocks', 'medias'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'variant' => $this->formatVariantForJson($variant)
        ]);
    }

    /**
     * Obtenir la liste des variants pour le tableau
     */
    public function getVariants($draftId)
    {
        $draft = Article::where('id', $draftId)
            ->where('status', 'draft')
            ->firstOrFail();

        $variants = Variant::where('article_id', $draft->id)
            ->with(['attributeValues.attribute', 'stocks', 'medias'])
            ->get();

        $formattedVariants = $variants->map(function($variant) {
            return $this->formatVariantForJson($variant);
        });

        return response()->json([
            'success' => true,
            'variants' => $formattedVariants
        ]);
    }

    /**
     * Finaliser l'article (passer de draft à active)
     */
    private function finalizeArticle(Article $draft)
    {
        Log::info('[finalizeArticle] Début', ['article_id' => $draft->id]);
        // Vérifier qu'il y a au moins un variant
        if ($draft->variants()->count() === 0) {
            Log::warning('[finalizeArticle] Aucun variant trouvé', ['article_id' => $draft->id]);
            throw new \Exception("L'article doit avoir au moins un variant.");
        }
        // Créer une transaction technique pour l'initialisation du stock
        $transaction = new \App\Models\Transaction();
        $transaction->transaction_number = 'INIT-' . now()->format('YmdHis') . '-' . $draft->id;
        $transaction->transaction_type = 'init_stock';
        $transaction->status = 'completed';
        $transaction->payment_status = 'paid';
        $transaction->cashier_id = auth()->id() ?? 1;
        $transaction->cash_register_id = null;
        $transaction->currency = 'EUR';
        $transaction->notes = 'Initialisation du stock à l\'activation de l\'article';
        $transaction->total_amount = 0;
        $transaction->save();
        Log::info('[finalizeArticle] Transaction technique créée', ['transaction_id' => $transaction->id]);
        // Générer le code-barres pour chaque variant sans code-barres
        foreach ($draft->variants as $variant) {
            Log::info('[finalizeArticle] Traitement variant', ['variant_id' => $variant->id]);
            if (empty($variant->barcode)) {
                $variant->barcode = VariantService::generateCustomBarcode();
                $variant->save();
                Log::info('[finalizeArticle] Code-barres généré', ['variant_id' => $variant->id, 'barcode' => $variant->barcode]);
            }
            // === Mouvement de stock initial ===
            $stock = $variant->stocks->first();
            if ($stock && $stock->quantity > 0) {
                Log::info('[finalizeArticle] Stock initial détecté', ['variant_id' => $variant->id, 'stock_id' => $stock->id, 'quantity' => $stock->quantity]);
                // Créer un TransactionItem technique pour l'initialisation
                $transactionItem = new \App\Models\TransactionItem();
                $transactionItem->transaction_id = $transaction->id;
                $transactionItem->variant_id = $variant->id;
                $transactionItem->stock_id = $stock->id;
                $transactionItem->article_name = $draft->name;
                $transactionItem->variant_reference = $variant->reference;
                $transactionItem->quantity = $stock->quantity;
                $transactionItem->unit_price_ht = $stock->buy_price;
                $transactionItem->unit_price_ttc = $stock->buy_price;
                $transactionItem->total_price_ht = $stock->quantity * $stock->buy_price;
                $transactionItem->total_price_ttc = $stock->quantity * $stock->buy_price;
                $transactionItem->tax_rate = 0;
                $transactionItem->tax_amount = 0;
                $transactionItem->discount_rate = 0;
                $transactionItem->discount_amount = 0;
                $transactionItem->total_cost = $stock->quantity * $stock->buy_price;
                $transactionItem->margin = 0;
                $transactionItem->source = 'init_stock';
                $transactionItem->save();
                Log::info('[finalizeArticle] TransactionItem créé', ['transaction_item_id' => $transactionItem->id]);

                // Mouvement de stock (entrée)
                $movement = \App\Models\TransactionStockMovement::create([
                    'transaction_item_id' => $transactionItem->id,
                    'stock_id' => $stock->id,
                    'quantity_used' => -$stock->quantity, // Entrée
                    'cost_price' => $stock->buy_price,
                    'total_cost' => $stock->quantity * $stock->buy_price,
                    'lot_reference' => $stock->lot_reference,
                ]);
                Log::info('[finalizeArticle] Mouvement de stock créé', ['movement_id' => $movement->id]);
            } else {
                Log::info('[finalizeArticle] Pas de stock initial pour ce variant', ['variant_id' => $variant->id]);
            }
        }
        $draft->update(['status' => 'active']);
        Log::info('[finalizeArticle] Article activé', ['article_id' => $draft->id]);
    }

    /**
     * Formater un variant pour JSON
     */
    private function formatVariantForJson($variant)
    {
        return [
            'id' => $variant->id,
            'barcode' => $variant->barcode,
            'reference' => $variant->reference,
            'sell_price' => $variant->sell_price,
            'buy_price' => $variant->buy_price,
            'attributes_display' => $variant->attributeValues->map(function($av) {
                return $av->attribute->name . ': ' . $av->value;
            })->implode(', '),
            'attributes' => $variant->attributeValues->map(function($av) {
                return [
                    'attribute_id' => $av->attribute_id,
                    'attribute_value_id' => $av->id,
                    'value' => $av->value,
                    'second_value' => $av->secondValue
                ];
            }),
            'stock' => $variant->stocks->first() ? [
                'quantity' => $variant->stocks->first()->quantity,
                'buy_price' => $variant->stocks->first()->buy_price,
                'lot_reference' => $variant->stocks->first()->lot_reference,
                'expiry_date' => $variant->stocks->first()->expiry_date,
                'total_value' => $variant->stocks->first()->quantity * $variant->stocks->first()->buy_price
            ] : null,
            'images' => $variant->medias->map(function($media) {
                return [
                    'url' => Storage::url($media->path),
                    'type' => $media->type
                ];
            })
        ];
    }

    public function checkBarcodeUnique(Request $request)
    {
        $barcode = $request->get('barcode');
        $variantId = $request->get('variant_id');

        $query = Variant::where('barcode', $barcode);

        if ($variantId) {
            $query->where('id', '!=', $variantId);
        }

        $query->whereHas('article', function($q) {
            $q->where('status', '!=', Article::STATUS_DELETED);
        });

        return response()->json([
            'available' => !$query->exists()
        ]);
    }

    public function uploadVariantImage(Request $request, $draftId, $variantId)
    {
        $variant = Variant::where('id', $variantId)->where('article_id', $draftId)->firstOrFail();
        if ($request->isMethod('delete')) {
            // Supprimer l'image existante
            $media = $variant->medias()->where('type', 'image')->first();
            if ($media) {
                $media->deleteWithFile();
            }
            return response()->json(['success' => true]);
        }
        $request->validate([
            'image' => 'required|image|max:25600', // 25 Mo
        ]);
        // Supprimer l'ancienne image si elle existe
        $media = $variant->medias()->where('type', 'image')->first();
        if ($media) {
            $media->deleteWithFile();
        }
        $file = $request->file('image');
        $articleId = $variant->article_id;
        $year = now()->format('Y');
        $month = now()->format('m');
        $ext = $file->getClientOriginalExtension();
        $path = "article/{$year}/{$month}/{$articleId}/img_variant_{$variant->id}.{$ext}";
        // Stocker sur le disque public (storage/app/public/...)
        $file->storeAs("article/{$year}/{$month}/{$articleId}", "img_variant_{$variant->id}.{$ext}", 'public');
        $media = $variant->medias()->create([
            'path' => $path,
            'type' => 'image',
        ]);
        return response()->json([
            'success' => true,
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}
