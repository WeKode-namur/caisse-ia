<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Discount;
use App\Models\GiftCard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Retourne le contenu du panier
     */
    public function index()
    {
        $cart = session('register_cart', []);
        $customer = session('register_customer');
        $discounts = session('register_discounts', []);

        return response()->json([
            'success' => true,
            'cart' => $this->formatCartItems($cart),
            'customer' => $customer,
            'discounts' => $discounts,
            'totals' => $this->calculateTotals($cart, $discounts)
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

        $variant = Variant::with(['article', 'stocks' => function($query) {
            $query->where('quantity', '>', 0)->orderBy('expiry_date');
        }])->findOrFail($request->variant_id);

        // Vérifier le stock disponible
        $availableStock = $variant->stocks->sum('quantity');
        $quantity = $request->quantity ?? 1;

        if ($quantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => "Stock insuffisant. Disponible: {$availableStock}"
            ], 422);
        }

        // Générer un ID unique pour l'item du panier (convertir en string)
        $cartItemId = (string) Str::uuid();

        // Récupérer le panier actuel
        $cart = session('register_cart', []);

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

        // Ajouter l'item au panier
        $cart[$cartItemId] = [
            'id' => $cartItemId,
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
            'attributes' => $this->getVariantAttributes($variant),
            'added_at' => now()->toISOString()
        ];

        // Sauvegarder le panier
        session(['register_cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Article ajouté au panier',
            'item' => $this->formatCartItem($cart[$cartItemId]),
            'cart_totals' => $this->calculateTotals($cart, session('register_discounts', []))
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

        $cart = session('register_cart', []);

        if (!isset($cart[$itemId])) {
            return response()->json([
                'success' => false,
                'message' => 'Article non trouvé dans le panier'
            ], 404);
        }

        $item = $cart[$itemId];
        $variant = Variant::with('stocks')->find($item['variant_id']);

        // Vérifier le stock
        $availableStock = $variant->stocks->sum('quantity');
        if ($request->quantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => "Stock insuffisant. Disponible: {$availableStock}"
            ], 422);
        }

        // Mettre à jour l'item
        $cart[$itemId]['quantity'] = $request->quantity;

        if ($request->has('price')) {
            $cart[$itemId]['unit_price'] = $request->price;
        }

        $cart[$itemId]['total_price'] = $cart[$itemId]['unit_price'] * $cart[$itemId]['quantity'];

        session(['register_cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Article mis à jour',
            'item' => $this->formatCartItem($cart[$itemId]),
            'cart_totals' => $this->calculateTotals($cart, session('register_discounts', []))
        ]);
    }

    /**
     * Supprime un article du panier
     */
    public function removeItem($itemId)
    {
        $cart = session('register_cart', []);

        if (!isset($cart[$itemId])) {
            return response()->json([
                'success' => false,
                'message' => 'Article non trouvé dans le panier'
            ], 404);
        }

        unset($cart[$itemId]);
        session(['register_cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Article supprimé du panier',
            'cart_totals' => $this->calculateTotals($cart, session('register_discounts', []))
        ]);
    }

    /**
     * Vide complètement le panier
     */
    public function clear()
    {
        session()->forget(['register_cart', 'register_customer', 'register_discounts']);

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
        $cart = session('register_cart', []);
        $discounts = session('register_discounts', []);

        return response()->json([
            'success' => true,
            'totals' => $this->calculateTotals($cart, $discounts)
        ]);
    }

    /**
     * Sélectionne un client pour la transaction
     */
    public function selectCustomer(Request $request)
    {
        // Vérifier si la gestion des clients est activée
        if (!config('app.register_customer_management', false)) {
            return response()->json([
                'success' => false,
                'message' => 'La gestion des clients n\'est pas activée'
            ], 403);
        }

        $request->validate([
            'customer_id' => 'required_without:company_id|exists:customers,id',
            'company_id' => 'required_without:customer_id|exists:companies,id'
        ]);

        $customer = null;
        if ($request->customer_id) {
            $customer = Customer::find($request->customer_id);
        } elseif ($request->company_id) {
            $customer = Company::find($request->company_id);
        }

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Client non trouvé'
            ], 404);
        }

        session([
            'register_customer' => [
                'type' => $request->customer_id ? 'customer' : 'company',
                'id' => $customer->id,
                'name' => $customer->first_name ?? $customer->name,
                'email' => $customer->email,
                'loyalty_points' => $customer->loyalty_points ?? 0
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Client sélectionné',
            'customer' => session('register_customer')
        ]);
    }

    /**
     * Retire le client de la transaction
     */
    public function removeCustomer()
    {
        // Vérifier si la gestion des clients est activée
        if (!config('app.register_customer_management', false)) {
            return response()->json([
                'success' => false,
                'message' => 'La gestion des clients n\'est pas activée'
            ], 403);
        }

        session()->forget('register_customer');

        return response()->json([
            'success' => true,
            'message' => 'Client retiré de la transaction'
        ]);
    }

    /**
     * Recherche des clients
     */
    public function searchCustomers(Request $request)
    {
        // Vérifier si la gestion des clients est activée
        if (!config('app.register_customer_management', false)) {
            return response()->json([
                'success' => false,
                'message' => 'La gestion des clients n\'est pas activée'
            ], 403);
        }

        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $query = $request->query;

        // Recherche dans les clients
        $customers = Customer::where(function($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('customer_number', 'like', "%{$query}%");
        })->where('is_active', true)->limit(10)->get();

        // Recherche dans les entreprises
        $companies = Company::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('legal_name', 'like', "%{$query}%")
                ->orWhere('company_number', 'like', "%{$query}%")
                ->orWhere('vat_number', 'like', "%{$query}%");
        })->where('is_active', true)->limit(10)->get();

        return response()->json([
            'success' => true,
            'customers' => $customers->map(function($customer) {
                return [
                    'id' => $customer->id,
                    'type' => 'customer',
                    'name' => $customer->first_name . ' ' . $customer->last_name,
                    'email' => $customer->email,
                    'number' => $customer->customer_number,
                    'loyalty_points' => $customer->loyalty_points
                ];
            }),
            'companies' => $companies->map(function($company) {
                return [
                    'id' => $company->id,
                    'type' => 'company',
                    'name' => $company->name,
                    'email' => $company->email,
                    'number' => $company->company_number,
                    'vat_number' => $company->vat_number
                ];
            })
        ]);
    }

    /**
     * Crée un client rapidement
     */
    public function createQuickCustomer(Request $request)
    {
        // Vérifier si la gestion des clients est activée
        if (!config('app.register_customer_management', false)) {
            return response()->json([
                'success' => false,
                'message' => 'La gestion des clients n\'est pas activée'
            ], 403);
        }

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20'
        ]);

        try {
            $customer = Customer::create([
                'customer_number' => $this->generateCustomerNumber(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'is_active' => true
            ]);

            // Sélectionner automatiquement le client créé
            session([
                'register_customer' => [
                    'type' => 'customer',
                    'id' => $customer->id,
                    'name' => $customer->first_name . ' ' . $customer->last_name,
                    'email' => $customer->email,
                    'loyalty_points' => 0
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client créé et sélectionné',
                'customer' => session('register_customer')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du client: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les remises disponibles
     */
    public function getAvailableDiscounts()
    {
        $discounts = Discount::active()
            ->validAt(now())
            ->available()
            ->orderBy('name')
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
                    'min_amount' => $discount->min_amount,
                    'max_discount' => $discount->max_discount,
                    'applicable_to' => $discount->applicable_to,
                    'usage_percentage' => $discount->usage_percentage
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
            'discount_id' => 'nullable|exists:discounts,id',
            'discount_code' => 'nullable|string',
            'target_item_id' => 'nullable|string'
        ]);

        $discount = null;

        if ($request->discount_id) {
            $discount = Discount::findOrFail($request->discount_id);
        } elseif ($request->discount_code) {
            $discount = Discount::where('code', $request->discount_code)
                ->active()
                ->validAt(now())
                ->available()
                ->first();

            if (!$discount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Code de remise invalide ou expiré'
                ], 404);
            }
        }

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Remise non trouvée'
            ], 404);
        }

        // Calculer le montant de la remise
        $cart = session('register_cart', []);
        $cartTotal = collect($cart)->sum('total_price');

        if ($cartTotal < $discount->min_amount) {
            return response()->json([
                'success' => false,
                'message' => "Montant minimum requis: {$discount->min_amount}€"
            ], 422);
        }

        $discountAmount = $discount->calculateDiscount($cartTotal);

        // Ajouter la remise à la session
        $discounts = session('register_discounts', []);
        $discountId = (string) Str::uuid(); // Convertir en string

        $discounts[$discountId] = [
            'id' => $discountId,
            'discount_id' => $discount->id,
            'name' => $discount->name,
            'code' => $discount->code,
            'type' => $discount->type,
            'value' => $discount->value,
            'amount' => $discountAmount,
            'applied_to' => 'total',
            'target_item_id' => $request->target_item_id
        ];

        session(['register_discounts' => $discounts]);

        return response()->json([
            'success' => true,
            'message' => 'Remise appliquée',
            'discount' => $discounts[$discountId],
            'cart_totals' => $this->calculateTotals($cart, $discounts)
        ]);
    }

    /**
     * Supprime une remise
     */
    public function removeDiscount($discountId)
    {
        $discounts = session('register_discounts', []);

        if (!isset($discounts[$discountId])) {
            return response()->json([
                'success' => false,
                'message' => 'Remise non trouvée'
            ], 404);
        }

        unset($discounts[$discountId]);
        session(['register_discounts' => $discounts]);

        return response()->json([
            'success' => true,
            'message' => 'Remise supprimée',
            'cart_totals' => $this->calculateTotals(session('register_cart', []), $discounts)
        ]);
    }

    /**
     * Applique une remise manuelle
     */
    public function applyManualDiscount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'target_item_id' => 'nullable|string'
        ]);

        $cart = session('register_cart', []);
        $cartTotal = collect($cart)->sum('total_price');

        // Calculer le montant de la remise
        if ($request->type === 'percentage') {
            $discountAmount = $cartTotal * ($request->value / 100);
        } else {
            $discountAmount = min($request->value, $cartTotal);
        }

        // Ajouter la remise manuelle
        $discounts = session('register_discounts', []);
        $discountId = (string) Str::uuid(); // Convertir en string

        $discounts[$discountId] = [
            'id' => $discountId,
            'discount_id' => null,
            'name' => $request->name,
            'code' => null,
            'type' => 'manual',
            'value' => $request->value,
            'amount' => $discountAmount,
            'applied_to' => 'total',
            'target_item_id' => $request->target_item_id
        ];

        session(['register_discounts' => $discounts]);

        return response()->json([
            'success' => true,
            'message' => 'Remise manuelle appliquée',
            'discount' => $discounts[$discountId],
            'cart_totals' => $this->calculateTotals($cart, $discounts)
        ]);
    }

    /**
     * Recherche une carte cadeau
     */
    public function findGiftCard($code)
    {
        $giftCard = GiftCard::where('code', $code)
            ->usable()
            ->first();

        if (!$giftCard) {
            return response()->json([
                'success' => false,
                'message' => 'Carte cadeau non trouvée ou non utilisable'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'gift_card' => [
                'id' => $giftCard->id,
                'code' => $giftCard->code,
                'remaining_amount' => $giftCard->remaining_amount,
                'expires_at' => $giftCard->expires_at?->format('Y-m-d'),
                'owner' => $giftCard->owner ? [
                    'name' => $giftCard->owner->first_name ?? $giftCard->owner->name,
                    'email' => $giftCard->owner->email
                ] : null
            ]
        ]);
    }

    /**
     * Utilise une carte cadeau
     */
    public function useGiftCard(Request $request)
    {
        $request->validate([
            'gift_card_code' => 'required|string',
            'amount' => 'required|numeric|min:0.01'
        ]);

        $giftCard = GiftCard::where('code', $request->gift_card_code)
            ->usable()
            ->first();

        if (!$giftCard) {
            return response()->json([
                'success' => false,
                'message' => 'Carte cadeau non trouvée ou non utilisable'
            ], 404);
        }

        if ($request->amount > $giftCard->remaining_amount) {
            return response()->json([
                'success' => false,
                'message' => "Solde insuffisant. Disponible: {$giftCard->remaining_amount}€"
            ], 422);
        }

        try {
            // Utiliser la carte cadeau (sera finalisé lors de la transaction)
            session(['register_gift_card' => [
                'id' => $giftCard->id,
                'code' => $giftCard->code,
                'amount_to_use' => $request->amount
            ]]);

            return response()->json([
                'success' => true,
                'message' => 'Carte cadeau prête à être utilisée',
                'gift_card_usage' => session('register_gift_card')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'utilisation de la carte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crée une nouvelle carte cadeau
     */
    public function createGiftCard(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'customer_id' => 'nullable|exists:customers,id',
            'company_id' => 'nullable|exists:companies,id',
            'message' => 'nullable|string|max:500'
        ]);

        try {
            $giftCard = GiftCard::create([
                'code' => GiftCard::generateUniqueCode(),
                'initial_amount' => $request->amount,
                'remaining_amount' => $request->amount,
                'customer_id' => $request->customer_id,
                'company_id' => $request->company_id,
                'issued_by' => auth()->id(),
                'message' => $request->message,
                'is_active' => true
            ]);

            // Enregistrer la transaction d'émission
            $giftCard->giftCardTransactions()->create([
                'transaction_type' => 'issued',
                'amount' => $request->amount,
                'balance_before' => 0,
                'balance_after' => $request->amount,
                'processed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Carte cadeau créée',
                'gift_card' => [
                    'id' => $giftCard->id,
                    'code' => $giftCard->code,
                    'amount' => $giftCard->initial_amount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===== MÉTHODES PRIVÉES =====

    /**
     * Formate les items du panier pour l'affichage
     */
    private function formatCartItems($cart)
    {
        return collect($cart)->map(function ($item) {
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
            'unit_price' => number_format($item['unit_price'], 2),
            'total_price' => number_format($item['total_price'], 2),
            'attributes' => $item['attributes'] ?? null
        ];
    }

    /**
     * Calcule les totaux du panier
     */
    private function calculateTotals($cart, $discounts = [])
    {
        $subtotal = collect($cart)->sum('total_price');
        $itemsCount = collect($cart)->sum('quantity');
        $totalDiscount = collect($discounts)->sum('amount');
        $total = $subtotal - $totalDiscount;

        return [
            'items_count' => $itemsCount,
            'subtotal' => number_format($subtotal, 2),
            'discount_amount' => number_format($totalDiscount, 2),
            'total' => number_format($total, 2),
            'tax_amount' => number_format($total * 0.21, 2) // Simplifié, à améliorer
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
