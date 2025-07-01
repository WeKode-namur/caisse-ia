<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Discount;
use App\Models\GiftCard;
use App\Models\SessionItem;
use App\Services\RegisterSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Retourne le contenu du panier
     */
    public function index()
    {
        $cartData = RegisterSessionService::getCartData();
        $customer = RegisterSessionService::getCustomer();
        $discounts = RegisterSessionService::getDiscounts();

        return response()->json([
            'success' => true,
            'cart' => $this->formatCartItems($cartData['items']),
            'customer' => $customer,
            'discounts' => $discounts,
            'totals' => $cartData['totals']
        ]);
    }

    /**
     * Ajoute un article au panier
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'quantity' => 'numeric|min:0.001|max:999',
            'price_override' => 'nullable|numeric|min:0'
        ]);

        $variant = Variant::with([
            'article',
            'stocks' => fn($q) => $q->where('quantity', '>', 0)->orderBy('expiry_date'),
            'attributeValues.attribute'
        ])->findOrFail($request->variant_id);

        // Vérifier le stock disponible
        $availableStock = $variant->stocks->sum('quantity');
        $quantity = $request->quantity ?? 1;

        if ($quantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => "Stock insuffisant. Disponible: {$availableStock}"
            ], 422);
        }

        // Déterminer le prix
        $unitPrice = $request->price_override ?? (float) $variant->sell_price;

        // Sélectionner le stock à utiliser (FIFO)
        $stockToUse = $variant->stocks->first();

        if (!$stockToUse) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun stock disponible pour ce produit'
            ], 422);
        }

        // Vérifier si l'article existe déjà dans le panier (même variant_id et stock_id)
        $cart = RegisterSessionService::getCart();
        $existingItemId = null;
        foreach ($cart as $id => $item) {
            if (
                isset($item['variant_id'], $item['stock_id']) &&
                $item['variant_id'] == $variant->id &&
                $item['stock_id'] == $stockToUse->id
            ) {
                $existingItemId = $id;
                break;
            }
        }
        if ($existingItemId !== null) {
            // Incrémenter la quantité de l'item existant
            $newQuantity = $cart[$existingItemId]['quantity'] + $quantity;
            RegisterSessionService::updateCartItem($existingItemId, ['quantity' => $newQuantity]);
            $updatedCart = RegisterSessionService::getCart();
            $updatedItem = $updatedCart[$existingItemId] ?? null;
            return response()->json([
                'success' => true,
                'message' => 'Quantité augmentée pour l\'article déjà présent',
                'item' => $updatedItem ? $this->formatCartItem($updatedItem) : null,
                'cart_totals' => RegisterSessionService::calculateTotals()
            ]);
        }

        // Préparer l'item
        $itemData = [
            'variant_id' => $variant->id,
            'stock_id' => $stockToUse->id,
            'article_name' => $variant->article->name,
            'variant_reference' => $variant->reference,
            'barcode' => $variant->barcode,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $quantity,
            'tax_rate' => $variant->article->tva ?? 21,
            'cost_price' => (float) $stockToUse->buy_price,
            'variant_attributes' => $this->getVariantAttributes($variant)
        ];

        // Ajouter l'item au panier
        $itemId = RegisterSessionService::addCartItem($itemData);

        // Ajouter l'ID à itemData pour le formatage
        $itemData['id'] = $itemId;

        return response()->json([
            'success' => true,
            'message' => 'Article ajouté au panier',
            'item' => $this->formatCartItem($itemData),
            'cart_totals' => RegisterSessionService::calculateTotals()
        ]);
    }

    /**
     * Met à jour la quantité d'un article
     */
    public function updateItem(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.001|max:999',
            'price' => 'nullable|numeric|min:0'
        ]);

        $cart = RegisterSessionService::getCart();

        if (!isset($cart[$itemId])) {
            return response()->json([
                'success' => false,
                'message' => 'Article non trouvé dans le panier'
            ], 404);
        }

        $item = $cart[$itemId];

        // Si c'est un article temporaire (Article Z), on ne vérifie pas le stock ni le variant
        if (isset($item['attributes']['is_temporary']) && $item['attributes']['is_temporary']) {
            $updates = [
                'quantity' => $request->quantity
            ];
            if ($request->has('price')) {
                $updates['unit_price'] = $request->price;
                $updates['total_price'] = $request->price * $request->quantity;
            }
            $success = RegisterSessionService::updateCartItem($itemId, $updates);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour'
                ], 500);
            }

            $updatedCart = RegisterSessionService::getCart();
            $updatedItem = $updatedCart[$itemId] ?? null;

            return response()->json([
                'success' => true,
                'message' => 'Article Z mis à jour',
                'item' => $updatedItem ? $this->formatCartItem($updatedItem) : null,
                'cart_totals' => RegisterSessionService::calculateTotals()
            ]);
        }

        $variant = Variant::with('stocks')->find($item['variant_id']);

        // Vérifier le stock
        $availableStock = $variant->stocks->sum('quantity');
        if ($request->quantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => "Stock insuffisant. Disponible: {$availableStock}"
            ], 422);
        }

        // Préparer les mises à jour
        $updates = ['quantity' => $request->quantity];

        if ($request->has('price')) {
            $updates['unit_price'] = $request->price;
        }

        // Mettre à jour l'item
        $success = RegisterSessionService::updateCartItem($itemId, $updates);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }

        // Récupérer l'item mis à jour
        $updatedCart = RegisterSessionService::getCart();
        $updatedItem = $updatedCart[$itemId] ?? null;

        return response()->json([
            'success' => true,
            'message' => 'Article mis à jour',
            'item' => $updatedItem ? $this->formatCartItem($updatedItem) : null,
            'cart_totals' => RegisterSessionService::calculateTotals()
        ]);
    }

    /**
     * Supprime un article du panier
     */
    public function removeItem($itemId)
    {
        $success = RegisterSessionService::removeCartItem($itemId);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Article non trouvé dans le panier'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Article supprimé du panier',
            'cart_totals' => RegisterSessionService::calculateTotals()
        ]);
    }

    /**
     * Vide complètement le panier
     */
    public function clear()
    {
        RegisterSessionService::clearCart();

        return response()->json([
            'success' => true,
            'message' => 'Panier vidé'
        ]);
    }

    /**
     * Calcule et retourne les totaux
     */
    public function getTotals()
    {
        return response()->json([
            'success' => true,
            'totals' => RegisterSessionService::calculateTotals()
        ]);
    }

    /**
     * Récupère les remises disponibles
     */
    public function getAvailableDiscounts()
    {
        $discounts = Discount::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->get();

        return response()->json([
            'success' => true,
            'discounts' => $discounts->map(function($discount) {
                return [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'code' => $discount->code,
                    'type' => $discount->type,
                    'value' => $discount->value,
                    'description' => $discount->description
                ];
            })
        ]);
    }

    /**
     * Applique une remise
     */
    public function applyDiscount(Request $request)
    {
        $request->validate([
            'discount_code' => 'required|string',
            'target_item_id' => 'nullable|string'
        ]);

        $discount = Discount::where('code', $request->discount_code)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->first();

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Code de remise invalide ou expiré'
            ], 404);
        }

        $cart = RegisterSessionService::getCart();
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Le panier est vide'
            ], 422);
        }

        // Calculer le montant de la remise
        $subtotal = collect($cart)->sum('total_price');
        $discountAmount = 0;

        if ($discount->type === 'percentage') {
            $discountAmount = $subtotal * ($discount->value / 100);
        } else {
            $discountAmount = min($discount->value, $subtotal);
        }

        // Créer la remise
        $discountData = [
            'discount_id' => $discount->id,
            'name' => $discount->name,
            'code' => $discount->code,
            'type' => $discount->type,
            'value' => $discount->value,
            'amount' => $discountAmount,
            'applied_to' => 'total',
            'target_item_id' => $request->target_item_id
        ];

        $discountId = RegisterSessionService::addDiscount($discountData);

        return response()->json([
            'success' => true,
            'message' => 'Remise appliquée',
            'discount' => array_merge($discountData, ['id' => $discountId]),
            'cart_totals' => RegisterSessionService::calculateTotals()
        ]);
    }

    /**
     * Supprime une remise
     */
    public function removeDiscount($discountId)
    {
        $success = RegisterSessionService::removeDiscount($discountId);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Remise non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Remise supprimée',
            'cart_totals' => RegisterSessionService::calculateTotals()
        ]);
    }

    /**
     * Applique une remise manuelle
     */
    public function applyManualDiscount(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255'
        ]);

        $cart = RegisterSessionService::getCart();
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Le panier est vide'
            ], 422);
        }

        $subtotal = collect($cart)->sum('total_price');
        $discountAmount = min($request->amount, $subtotal);

        $discountData = [
            'discount_id' => null,
            'name' => $request->description,
            'code' => 'MANUAL',
            'type' => 'fixed',
            'value' => $request->amount,
            'amount' => $discountAmount,
            'applied_to' => 'total'
        ];

        $discountId = RegisterSessionService::addDiscount($discountData);

        return response()->json([
            'success' => true,
            'message' => 'Remise manuelle appliquée',
            'discount' => array_merge($discountData, ['id' => $discountId]),
            'cart_totals' => RegisterSessionService::calculateTotals()
        ]);
    }

    /**
     * Applique une remise personnalisée (pourcentage ou montant fixe)
     */
    public function applyCustomDiscount(Request $request)
    {
        $request->validate([
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255'
        ]);

        $cart = RegisterSessionService::getCart();
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Le panier est vide'
            ], 422);
        }

        $subtotal = collect($cart)->sum('total_price');
        $discountAmount = 0;
        if ($request->type === 'percentage') {
            $discountAmount = $subtotal * ($request->value / 100);
        } else {
            $discountAmount = min($request->value, $subtotal);
        }

        $discountData = [
            'discount_id' => null,
            'name' => $request->description ?? ($request->type === 'percentage' ? 'Remise personnalisée' : 'Remise fixe'),
            'code' => 'CUSTOM',
            'type' => $request->type,
            'value' => $request->value,
            'amount' => $discountAmount,
            'applied_to' => 'total'
        ];

        $discountId = RegisterSessionService::addDiscount($discountData);

        return response()->json([
            'success' => true,
            'message' => 'Remise appliquée',
            'discount' => array_merge($discountData, ['id' => $discountId]),
            'cart_totals' => RegisterSessionService::calculateTotals()
        ]);
    }

    /**
     * Ajoute un article temporaire (Article Z) au panier
     */
    public function addTemporaryItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0.01',
        ]);

        $itemData = [
            'variant_id' => null,
            'stock_id' => null,
            'article_name' => $request->name,
            'variant_reference' => null,
            'barcode' => null,
            'quantity' => 1,
            'unit_price' => $request->price,
            'total_price' => $request->price,
            'tax_rate' => 21, // TVA par défaut
            'cost_price' => 0,
            'attributes' => [
                'is_temporary' => true,
                'description' => $request->description,
            ],
        ];

        $itemId = \App\Services\RegisterSessionService::addCartItem($itemData);
        $itemData['id'] = $itemId;

        return response()->json([
            'success' => true,
            'message' => "Article Z ajouté au panier",
            'item' => $this->formatCartItem($itemData),
            'cart_totals' => \App\Services\RegisterSessionService::calculateTotals(),
        ]);
    }

    /**
     * Lie un client (particulier ou entreprise) à la session de caisse
     */
    public function selectCustomer(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'client_type' => 'required|in:customer,company',
        ]);

        $client = $request->client_type === 'customer'
            ? Customer::find($request->client_id)
            : Company::find($request->client_id);

        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Client introuvable.'], 404);
        }

        $customerData = [
            'id' => $client->id,
            'type' => $request->client_type,
            'name' => $request->client_type === 'customer' ? ($client->first_name . ' ' . $client->last_name) : $client->name,
            'email' => $client->email,
            'phone' => $client->phone,
            'loyalty_points' => $client->loyalty_points ?? 0,
        ];
        if ($request->client_type === 'company') {
            $customerData['company_number_be'] = $client->company_number_be;
        }

        \App\Services\RegisterSessionService::setCustomer($customerData);

        return response()->json(['success' => true, 'customer' => $customerData]);
    }

    /**
     * Dissocie le client de la session de caisse
     */
    public function removeCustomer()
    {
        RegisterSessionService::removeCustomer();
        return response()->json(['success' => true]);
    }

    /**
     * Retourne le client lié à la session de caisse (pour affichage panier)
     */
    public function showCustomer()
    {
        $customer = RegisterSessionService::getCustomer();
        return response()->json([
            'customer' => $customer
        ]);
    }

    // ===== MÉTHODES PRIVÉES =====

    /**
     * Formate les items du panier
     */
    private function formatCartItems($cart)
    {
        return collect($cart)->map(function($item) {
            return $this->formatCartItem($item);
        })->values();
    }

    /**
     * Formate un item du panier
     */
    private function formatCartItem($item)
    {
        return [
            'id' => $item['id'],
            'article_name' => $item['article_name'],
            'variant_reference' => $item['variant_reference'],
            'barcode' => $item['barcode'],
            'quantity' => $item['quantity'],
            'unit_price' => number_format($item['unit_price'], 2, '.', ''),
            'total_price' => number_format($item['total_price'], 2, '.', ''),
            'cost_price' => isset($item['cost_price']) ? number_format($item['cost_price'], 2, '.', '') : null,
            'tax_rate' => isset($item['tax_rate']) ? $item['tax_rate'] : null,
            'attributes' => $item['variant_attributes'] ?? null
        ];
    }

    /**
     * Récupère les attributs du variant
     */
    private function getVariantAttributes($variant)
    {
        // À implémenter selon votre logique d'attributs
        return null;
    }
}
