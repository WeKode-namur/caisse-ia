<?php

return [
    'generator' => [
        'barcode' => env('ENABLE_BARCODE_GENERATOR', false),
    ],
    'article' => [
        'seuil' => env('ARTICLE_SEUIL', 5),
    ],
];
