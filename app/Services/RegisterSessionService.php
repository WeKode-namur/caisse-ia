<?php

namespace App\Services;

use Illuminate\Support\Str;

class RegisterSessionService
{
    const CART_KEY = 'register_cart';
    const CUSTOMER_KEY = 'register_customer';
    const DISCOUNTS_KEY = 'register_discounts';
    const CASH_REGISTER_KEY = 'current_cash_register_id';

    /**
     * Récupère le panier de la session
     */
    public static function getCart()
    {
        return session(self::CART_KEY, []);
    }

    /**
     * Sauvegarde le panier en session
     */
    public static function setCart(array $cart)
    {
        session([self::CART_KEY => $cart]);
    }

    /**
     * Ajoute un item au panier
     */
    public static function addCartItem(array $item)
    {
        $cart = self::getCart();
        $itemId = Str::uuid();
        $item['id'] = $itemId;
        $cart[$itemId] = $item;
        self::setCart($cart);
        return $itemId;
    }

    /**
     * Met à jour un item du panier
     */
    public static function updateCartItem($itemId, array $updates)
    {
        $cart = self::getCart();
        if (isset($cart[$itemId])) {
            $cart[$itemId] = array_merge($cart[$itemId], $updates);
            self::setCart($cart);
            return true;
        }
        return false;
    }

    /**
     * Supprime un item du panier
     */
    public static function removeCartItem($itemId)
    {
        $cart = self::getCart();
        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            self::setCart($cart);
            return true;
        }
        return false;
    }

    /**
     * Vide le panier
     */
    public static function clearCart()
    {
        session()->forget(self::CART_KEY);
    }

    /**
     * Récupère le client sélectionné
     */
    public static function getCustomer()
    {
        // Vérifier si la gestion des clients est activée
        if (!config('app.register_customer_management', false)) {
            return null;
        }

        return session(self::CUSTOMER_KEY);
    }

    /**
     * Définit le client
     */
    public static function setCustomer(array $customer)
    {
        // Vérifier si la gestion des clients est activée
        if (!config('app.register_customer_management', false)) {
            return false;
        }

        session([self::CUSTOMER_KEY => $customer]);
        return true;
    }

    /**
     * Supprime le client
     */
    public static function removeCustomer()
    {
        // Vérifier si la gestion des clients est activée
        if (!config('app.register_customer_management', false)) {
            return false;
        }

        session()->forget(self::CUSTOMER_KEY);
        return true;
    }

    /**
     * Récupère les remises appliquées
     */
    public static function getDiscounts()
    {
        return session(self::DISCOUNTS_KEY, []);
    }

    /**
     * Ajoute une remise
     */
    public static function addDiscount(array $discount)
    {
        $discounts = self::getDiscounts();
        $discountId = Str::uuid();
        $discount['id'] = $discountId;
        $discounts[$discountId] = $discount;
        session([self::DISCOUNTS_KEY => $discounts]);
        return $discountId;
    }

    /**
     * Supprime une remise
     */
    public static function removeDiscount($discountId)
    {
        $discounts = self::getDiscounts();
        if (isset($discounts[$discountId])) {
            unset($discounts[$discountId]);
            session([self::DISCOUNTS_KEY => $discounts]);
            return true;
        }
        return false;
    }

    /**
     * Vide toutes les remises
     */
    public static function clearDiscounts()
    {
        session()->forget(self::DISCOUNTS_KEY);
    }

    /**
     * Calcule les totaux du panier
     */
    public static function calculateTotals()
    {
        $cart = self::getCart();
        $discounts = self::getDiscounts();

        $subtotal = collect($cart)->sum('total_price');
        $itemsCount = collect($cart)->sum('quantity');
        $totalDiscount = collect($discounts)->sum('amount');
        $total = max(0, $subtotal - $totalDiscount);

        // Calcul simplifié de la TVA (à améliorer selon vos besoins)
        $taxAmount = $total * 0.21 / 1.21;
        $subtotalHT = $total - $taxAmount;

        return [
            'items_count' => $itemsCount,
            'total_items' => count($cart),
            'subtotal_ht' => round($subtotalHT, 2),
            'subtotal_ttc' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'discount_amount' => round($totalDiscount, 2),
            'total_amount' => round($total, 2)
        ];
    }

    /**
     * Vide complètement la session de caisse
     */
    public static function clearSession()
    {
        session()->forget([
            self::CART_KEY,
            self::CUSTOMER_KEY,
            self::DISCOUNTS_KEY
        ]);
    }

    /**
     * Vérifie si le panier est vide
     */
    public static function isCartEmpty()
    {
        return empty(self::getCart());
    }

    /**
     * Récupère la caisse actuelle
     */
    public static function getCurrentCashRegister()
    {
        return session(self::CASH_REGISTER_KEY);
    }

    /**
     * Définit la caisse actuelle
     */
    public static function setCurrentCashRegister($cashRegisterId)
    {
        session([self::CASH_REGISTER_KEY => $cashRegisterId]);
    }

    /**
     * Exporte les données de session pour une transaction
     */
    public static function exportSessionData()
    {
        return [
            'cart' => self::getCart(),
            'customer' => self::getCustomer(),
            'discounts' => self::getDiscounts(),
            'totals' => self::calculateTotals(),
            'cash_register_id' => self::getCurrentCashRegister(),
            'session_id' => session()->getId(),
            'user_id' => auth()->id()
        ];
    }
}
