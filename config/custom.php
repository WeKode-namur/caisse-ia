<?php

use App\Services\SettingsService;

return [
    'generator' => [
        'barcode' => (function () {
            $value = SettingsService::get('generator.barcode');
            return ($value === false && env('ENABLE_BARCODE_GENERATOR') !== null) ? env('ENABLE_BARCODE_GENERATOR') : $value;
        })(),
    ],
    'article' => [
        'seuil' => (function () {
            $value = SettingsService::get('article.seuil');
            return ($value === 5 && env('ARTICLE_SEUIL') !== null) ? env('ARTICLE_SEUIL') : $value;
        })(),
    ],
    'register' => [
        'customer_management' => (function () {
            $value = SettingsService::get('register.customer_management');
            return ($value === false && env('REGISTER_CUSTOMER_MANAGEMENT') !== null) ? env('REGISTER_CUSTOMER_MANAGEMENT') : $value;
        })(),
        'arrondissementMethod' => (function () {
            $value = SettingsService::get('register.arrondissement_method');
            return ($value === false && env('REGISTER_ARRONDISSEMENT_METHOD') !== null) ? env('REGISTER_ARRONDISSEMENT_METHOD') : $value;
        })(),
        'tva_default' => (function () {
            $value = SettingsService::get('register.tva_default');
            return $value !== null ? $value : (env('REGISTER_TVA_DEFAULT') !== null && env('REGISTER_TVA_DEFAULT') !== '' ? (int)env('REGISTER_TVA_DEFAULT') : null);
        })(),
    ],
    'email' => [
        'active' => (function () {
            $value = SettingsService::get('email.active');
            return ($value === false && env('MAIL_ACTIVE') !== null) ? env('MAIL_ACTIVE') : $value;
        })(),
    ],
    'barcode' => [
        'prefix_one' => (function () {
            $value = SettingsService::get('barcode.prefix_one');
            return ($value === 'WK' && env('PREFIX_ONE') !== null) ? env('PREFIX_ONE') : $value;
        })(),
        'prefix_two' => (function () {
            $value = SettingsService::get('barcode.prefix_two');
            return ($value === 'NAM' && env('PREFIX_TWO') !== null) ? env('PREFIX_TWO') : $value;
        })(),
    ],
    'referent_lot_optionnel' => (function () {
        $value = SettingsService::get('referent_lot_optionnel');
        return ($value === true && env('REFERENT_LOT_OPTIONNEL') !== null) ? env('REFERENT_LOT_OPTIONNEL') : $value;
    })(),
    'date_expiration_optionnel' => (function () {
        $value = SettingsService::get('date_expiration_optionnel');
        return ($value === true && env('DATE_EXPIRATION_OPTIONNEL') !== null) ? env('DATE_EXPIRATION_OPTIONNEL') : $value;
    })(),
    'item_stock_no_limit' => (function () {
        $value = SettingsService::get('item_stock_no_limit');
        return ($value === null && env('ITEM_STOCK_NO_LIMIT') !== null) ? env('ITEM_STOCK_NO_LIMIT') : $value;
    })(),
    'version' => [
        'current' => env('APP_VERSION', 'v0.0.1'),
        'check_from' => env('APP_VERSION_CHECK_FROM', 'v0.0.1'),
    ],
    'address' => [
        'street' => (function () {
            $value = SettingsService::get('company.address_street');
            return (empty($value) && env('CUSTOM_ADDRESS_STREET') !== null) ? env('CUSTOM_ADDRESS_STREET') : $value;
        })(),
        'postal' => (function () {
            $value = SettingsService::get('company.address_postal');
            return (empty($value) && env('CUSTOM_ADDRESS_POSTAL') !== null) ? env('CUSTOM_ADDRESS_POSTAL') : $value;
        })(),
        'city' => (function () {
            $value = SettingsService::get('company.address_city');
            return (empty($value) && env('CUSTOM_ADDRESS_CITY') !== null) ? env('CUSTOM_ADDRESS_CITY') : $value;
        })(),
        'country' => (function () {
            $value = SettingsService::get('company.address_country');
            return (empty($value) && env('CUSTOM_ADDRESS_COUNTRY') !== null) ? env('CUSTOM_ADDRESS_COUNTRY') : $value;
        })(),
    ],
    'tva' => (function () {
        $value = SettingsService::get('company.tva_number');
        return (empty($value) && env('CUSTOM_TVA_NUMBER') !== null) ? env('CUSTOM_TVA_NUMBER') : $value;
    })(),
    'phone' => (function () {
        $value = SettingsService::get('company.phone');
        return (empty($value) && env('CUSTOM_PHONE') !== null) ? env('CUSTOM_PHONE') : $value;
    })(),
    'loyalty_point_step' => (function () {
        $value = SettingsService::get('loyalty_point_step');
        $envValue = env('LOYALTY_POINT_STEP');
        return ($value === 1 && $envValue !== null && $envValue !== '') ? (int)$envValue : $value;
    })(),
    'items' => [
        'sousType' => (function () {
            $value = SettingsService::get('items.sous_type');
            return ($value === false && env('CUSTOM_ITEMS_SOUS_TYPE') !== null) ? env('CUSTOM_ITEMS_SOUS_TYPE') : $value;
        })(),
    ],
    'suppliers_enabled' => (function () {
        $value = SettingsService::get('suppliers_enabled');
        return ($value === false && env('SUPPLIERS_ENABLED') !== null) ? env('SUPPLIERS_ENABLED') : $value;
    })(),
];
