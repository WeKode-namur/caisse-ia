<?php

namespace App\Services;

use App\Models\RegisterSession;
use App\Models\SessionItem;
use Illuminate\Support\Str;

class RegisterSessionService
{
    const CART_KEY = 'register_cart';
    const CUSTOMER_KEY = 'register_customer';
    const DISCOUNTS_KEY = 'register_discounts';
    const CASH_REGISTER_KEY = 'current_cash_register_id';

    /**
     * Récupère ou crée une session pour l'utilisateur actuel
     */
    public static function getOrCreateSession(): RegisterSession
    {
        $sessionId = session()->getId();
        $userId = auth()->id();
        $cashRegisterId = session(self::CASH_REGISTER_KEY);

        $session = RegisterSession::firstOrCreate(
            ['id' => $sessionId],
            [
                'user_id' => $userId,
                'cash_register_id' => $cashRegisterId,
                'status' => 'active',
                'last_activity' => now()
            ]
        );

        // Mettre à jour l'activité
        $session->updateActivity();

        return $session;
    }

    /**
     * Récupère le panier de la session (depuis la base de données)
     */
    public static function getCart(): array
    {
        $session = self::getOrCreateSession();
        $items = $session->sessionItems()->with(['variant.article', 'stock'])->get();
        
        $cart = [];
        foreach ($items as $item) {
            // S'assurer que l'ID est une string
            $itemId = (string) $item->id;
            
            $cart[$itemId] = [
                'id' => $itemId,
                'variant_id' => $item->variant_id,
                'stock_id' => $item->stock_id,
                'article_name' => $item->article_name,
                'variant_reference' => $item->variant_reference,
                'barcode' => $item->barcode,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
                'tax_rate' => (float) $item->tax_rate,
                'cost_price' => (float) $item->cost_price,
                'attributes' => $item->attributes,
                'added_at' => $item->created_at->toISOString()
            ];
        }

        // Synchroniser avec la session PHP pour compatibilité
        session([self::CART_KEY => $cart]);
        
        return $cart;
    }

    /**
     * Récupère les données complètes du panier (items + totaux)
     */
    public static function getCartData(): array
    {
        $items = self::getCart();
        $totals = self::calculateTotals();

        return [
            'items' => $items,
            'totals' => $totals
        ];
    }

    /**
     * Sauvegarde le panier en base de données
     */
    public static function setCart(array $cart): void
    {
        $session = self::getOrCreateSession();
        
        // Supprimer les anciens items
        $session->sessionItems()->delete();
        
        // Ajouter les nouveaux items
        foreach ($cart as $itemId => $itemData) {
            SessionItem::create([
                'id' => $itemId,
                'session_id' => $session->id,
                'variant_id' => $itemData['variant_id'],
                'stock_id' => $itemData['stock_id'],
                'article_name' => $itemData['article_name'],
                'variant_reference' => $itemData['variant_reference'],
                'barcode' => $itemData['barcode'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'total_price' => $itemData['total_price'],
                'tax_rate' => $itemData['tax_rate'],
                'cost_price' => $itemData['cost_price'],
                'attributes' => $itemData['attributes'] ?? null
            ]);
        }

        // Mettre à jour les totaux de la session
        $session->updateTotals();
        
        // Synchroniser avec la session PHP
        session([self::CART_KEY => $cart]);
    }

    /**
     * Ajoute un item au panier
     */
    public static function addCartItem(array $item): string
    {
        $session = self::getOrCreateSession();
        $itemId = (string) Str::uuid();
        $item['id'] = $itemId;

        // Créer l'item en base
        SessionItem::create([
            'id' => $itemId,
            'session_id' => $session->id,
            'variant_id' => $item['variant_id'],
            'stock_id' => $item['stock_id'],
            'article_name' => $item['article_name'],
            'variant_reference' => $item['variant_reference'],
            'barcode' => $item['barcode'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'total_price' => $item['total_price'],
            'tax_rate' => $item['tax_rate'],
            'cost_price' => $item['cost_price'],
            'attributes' => $item['attributes'] ?? null
        ]);

        // Mettre à jour les totaux
        $session->updateTotals();

        // Ajouter à la session PHP (éviter la récursion)
        $cart = session(self::CART_KEY, []);
        $cart[$itemId] = $item;
        session([self::CART_KEY => $cart]);

        return $itemId;
    }

    /**
     * Met à jour un item du panier
     */
    public static function updateCartItem(string $itemId, array $updates): bool
    {
        $session = self::getOrCreateSession();
        $item = $session->sessionItems()->find($itemId);

        if (!$item) {
            return false;
        }

        // Mettre à jour en base
        $item->update($updates);
        $item->updateTotalPrice();

        // Mettre à jour les totaux de la session
        $session->updateTotals();

        // Mettre à jour la session PHP (éviter la récursion)
        $cart = session(self::CART_KEY, []);
        if (isset($cart[$itemId])) {
            $cart[$itemId] = array_merge($cart[$itemId], $updates);
            session([self::CART_KEY => $cart]);
        }

        return true;
    }

    /**
     * Supprime un item du panier
     */
    public static function removeCartItem(string $itemId): bool
    {
        $session = self::getOrCreateSession();
        $item = $session->sessionItems()->find($itemId);

        if (!$item) {
            return false;
        }

        // Supprimer de la base
        $item->delete();

        // Mettre à jour les totaux
        $session->updateTotals();

        // Supprimer de la session PHP (éviter la récursion)
        $cart = session(self::CART_KEY, []);
        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session([self::CART_KEY => $cart]);
        }

        return true;
    }

    /**
     * Vide le panier
     */
    public static function clearCart(): void
    {
        $session = self::getOrCreateSession();
        
        // Supprimer tous les items de la base
        $session->sessionItems()->delete();
        
        // Mettre à jour les totaux
        $session->updateTotals();
        
        // Vider la session PHP
        session()->forget(self::CART_KEY);
    }

    /**
     * Récupère le client sélectionné
     */
    public static function getCustomer(): ?array
    {
        // Vérifier si la gestion des clients est activée
        if (!config('app.register_customer_management', false)) {
            return null;
        }

        $session = self::getOrCreateSession();
        $customerData = $session->customer_data;

        if ($customerData) {
            session([self::CUSTOMER_KEY => $customerData]);
            return $customerData;
        }

        return session(self::CUSTOMER_KEY);
    }

    /**
     * Définit le client
     */
    public static function setCustomer(array $customer): bool
    {
        // Vérifier si la gestion des clients est activée
        if (!config('app.register_customer_management', false)) {
            return false;
        }

        $session = self::getOrCreateSession();
        $session->update([
            'customer_data' => $customer,
            'last_activity' => now()
        ]);

        session([self::CUSTOMER_KEY => $customer]);
        return true;
    }

    /**
     * Supprime le client
     */
    public static function removeCustomer(): bool
    {
        // Vérifier si la gestion des clients est activée
        if (!config('app.register_customer_management', false)) {
            return false;
        }

        $session = self::getOrCreateSession();
        $session->update([
            'customer_data' => null,
            'last_activity' => now()
        ]);

        session()->forget(self::CUSTOMER_KEY);
        return true;
    }

    /**
     * Récupère les remises appliquées
     */
    public static function getDiscounts(): array
    {
        $session = self::getOrCreateSession();
        $discountsData = $session->discounts_data;

        if ($discountsData) {
            session([self::DISCOUNTS_KEY => $discountsData]);
            return $discountsData;
        }

        return session(self::DISCOUNTS_KEY, []);
    }

    /**
     * Ajoute une remise
     */
    public static function addDiscount(array $discount): string
    {
        $session = self::getOrCreateSession();
        $discounts = self::getDiscounts();
        $discountId = (string) Str::uuid();
        $discount['id'] = $discountId;
        $discounts[$discountId] = $discount;

        $session->update([
            'discounts_data' => $discounts,
            'last_activity' => now()
        ]);

        session([self::DISCOUNTS_KEY => $discounts]);
        return $discountId;
    }

    /**
     * Supprime une remise
     */
    public static function removeDiscount(string $discountId): bool
    {
        $session = self::getOrCreateSession();
        $discounts = self::getDiscounts();

        if (!isset($discounts[$discountId])) {
            return false;
        }

        unset($discounts[$discountId]);

        $session->update([
            'discounts_data' => $discounts,
            'last_activity' => now()
        ]);

        session([self::DISCOUNTS_KEY => $discounts]);
        return true;
    }

    /**
     * Vide toutes les remises
     */
    public static function clearDiscounts(): void
    {
        $session = self::getOrCreateSession();
        $session->update([
            'discounts_data' => null,
            'last_activity' => now()
        ]);

        session()->forget(self::DISCOUNTS_KEY);
    }

    /**
     * Calcule les totaux du panier
     */
    public static function calculateTotals(): array
    {
        $cart = self::getCart();
        $discounts = self::getDiscounts();

        $subtotal = collect($cart)->sum('total_price');
        $itemsCount = collect($cart)->sum('quantity');
        $totalDiscount = 0;
        $discountsRecalculated = [];

        foreach ($discounts as $discountId => $discount) {
            $amount = 0;
            if (($discount['type'] ?? null) === 'percentage') {
                $amount = $subtotal * (($discount['value'] ?? 0) / 100);
            } elseif (($discount['type'] ?? null) === 'fixed') {
                $amount = min($discount['value'] ?? 0, $subtotal);
            }
            $discount['amount'] = round($amount, 2);
            $discountsRecalculated[$discountId] = $discount;
            $totalDiscount += $amount;
        }

        // Mettre à jour la session et la BDD avec les montants recalculés
        $session = self::getOrCreateSession();
        $session->update([
            'discounts_data' => $discountsRecalculated,
            'last_activity' => now()
        ]);
        session([self::DISCOUNTS_KEY => $discountsRecalculated]);

        $total = max(0, $subtotal - $totalDiscount);

        return [
            'items_count' => $itemsCount,
            'total' => round($total, 2),
            'total_discount' => round($totalDiscount, 2)
        ];
    }

    /**
     * Vide complètement la session de caisse
     */
    public static function clearSession(): void
    {
        $session = self::getOrCreateSession();
        
        // Supprimer tous les items
        $session->sessionItems()->delete();
        
        // Réinitialiser la session
        $session->update([
            'customer_data' => null,
            'discounts_data' => null,
            'total_amount' => 0,
            'items_count' => 0,
            'last_activity' => now()
        ]);

        // Vider la session PHP
        session()->forget([
            self::CART_KEY,
            self::CUSTOMER_KEY,
            self::DISCOUNTS_KEY
        ]);
    }

    /**
     * Vérifie si le panier est vide
     */
    public static function isCartEmpty(): bool
    {
        return empty(self::getCart());
    }

    /**
     * Récupère la caisse actuelle
     */
    public static function getCurrentCashRegister(): ?int
    {
        return session(self::CASH_REGISTER_KEY);
    }

    /**
     * Définit la caisse actuelle
     */
    public static function setCurrentCashRegister(int $cashRegisterId): void
    {
        $session = self::getOrCreateSession();
        $session->update([
            'cash_register_id' => $cashRegisterId,
            'last_activity' => now()
        ]);

        session([self::CASH_REGISTER_KEY => $cashRegisterId]);
    }

    /**
     * Exporte les données de session pour une transaction
     */
    public static function exportSessionData(): array
    {
        return [
            'cart' => self::getCart(),
            'customer' => self::getCustomer(),
            'discounts' => self::getDiscounts(),
            'totals' => self::calculateTotals(),
            'session_id' => session()->getId()
        ];
    }

    /**
     * Récupère les sessions en attente pour un utilisateur
     */
    public static function getPendingSessions(int $userId): array
    {
        return RegisterSession::where('user_id', $userId)
            ->where('status', 'active')
            ->where('items_count', '>', 0)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function($session) {
                return [
                    'id' => $session->id,
                    'items_count' => $session->items_count,
                    'total_amount' => $session->total_amount,
                    'last_activity' => $session->last_activity->format('d/m/Y H:i'),
                    'cash_register' => $session->cashRegister ? $session->cashRegister->name : 'N/A'
                ];
            })
            ->toArray();
    }

    /**
     * Restaure une session
     */
    public static function restoreSession(string $sessionId): bool
    {
        $session = RegisterSession::where('id', $sessionId)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return false;
        }

        // Restaurer les données en session PHP
        $cart = [];
        foreach ($session->sessionItems as $item) {
            $cart[$item->id] = [
                'id' => $item->id,
                'variant_id' => $item->variant_id,
                'stock_id' => $item->stock_id,
                'article_name' => $item->article_name,
                'variant_reference' => $item->variant_reference,
                'barcode' => $item->barcode,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
                'tax_rate' => (float) $item->tax_rate,
                'cost_price' => (float) $item->cost_price,
                'attributes' => $item->attributes,
                'added_at' => $item->created_at->toISOString()
            ];
        }

        session([
            self::CART_KEY => $cart,
            self::CUSTOMER_KEY => $session->customer_data,
            self::DISCOUNTS_KEY => $session->discounts_data ?? [],
            self::CASH_REGISTER_KEY => $session->cash_register_id
        ]);

        // Mettre à jour l'activité
        $session->updateActivity();

        return true;
    }
}
