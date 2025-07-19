<?php

namespace App\Helpers;

use App\Services\SettingsService;

class SettingsHelper
{
    /**
     * Récupère la TVA par défaut
     */
    public static function getDefaultTva()
    {
        return SettingsService::get('register.tva_default');
    }

    /**
     * Récupère un paramètre avec notation point
     */
    public static function get(string $key, $default = null)
    {
        return SettingsService::get($key, $default);
    }

    /**
     * Vérifie si la gestion des clients est activée
     */
    public static function isCustomerManagementEnabled(): bool
    {
        return SettingsService::get('register.customer_management', false);
    }

    /**
     * Vérifie si les fournisseurs sont activés
     */
    public static function isSuppliersEnabled(): bool
    {
        return SettingsService::get('suppliers_enabled', false);
    }

    /**
     * Vérifie si le générateur de codes-barres est activé
     */
    public static function isBarcodeGeneratorEnabled(): bool
    {
        return SettingsService::get('generator.barcode', false);
    }

    /**
     * Récupère le seuil d'alerte des articles
     */
    public static function getArticleSeuil(): int
    {
        return SettingsService::get('article.seuil', 5);
    }
}
