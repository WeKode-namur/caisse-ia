<?php

return [
    'generator' => [
        'barcode' => env('ENABLE_BARCODE_GENERATOR', false),
    ],
    'article' => [
        'seuil' => env('ARTICLE_SEUIL', 5),
    ],
    'register' => [
        'customer_management' => env('REGISTER_CUSTOMER_MANAGEMENT', false),
        'arrondissementMethod' => env('REGISTER_ARRONDISSEMENT_METHOD', false),
    ],
    'email' => [
        'active' => env('MAIL_ACTIVE', false),
    ],
    'barcode' => [
        'prefix_one' => env('PREFIX_ONE', 'WK'),
        'prefix_two' => env('PREFIX_TWO', 'NAM'),
    ],
    'referent_lot_optionnel' => env('REFERENT_LOT_OPTIONNEL', true),
    'date_expiration_optionnel' => env('DATE_EXPIRATION_OPTIONNEL', true),
    'version' => [
        'current' => env('APP_VERSION', 'v0.0.1'),
        'check_from' => env('APP_VERSION_CHECK_FROM', 'v0.0.1'),
    ],
    'address' => [
        'street' => env('CUSTOM_ADDRESS_STREET'),
        'postal' => env('CUSTOM_ADDRESS_POSTAL'),
        'city' => env('CUSTOM_ADDRESS_CITY'),
        'country' => env('CUSTOM_ADDRESS_COUNTRY'),
    ],
    'tva' => env('CUSTOM_TVA'),
    'loyalty_point_step' => env('LOYALTY_POINT_STEP', 1),
    'items' => [
      'sousType' => env('CUSTOM_ITEMS_SOUS_TYPE', false),
    ],
];
