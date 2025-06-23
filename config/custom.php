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
    ],
    'email' => [
        'active' => env('MAIL_ACTIVE', false),
    ],
];
