<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test ajout d'item au panier ===\n";

try {
    // Créer un item de test
    $item = [
        'variant_id' => 1,
        'stock_id' => 1,
        'article_name' => 'Test Article',
        'variant_reference' => 'TEST001',
        'barcode' => '123456789',
        'quantity' => 1,
        'unit_price' => 10.50,
        'total_price' => 10.50,
        'tax_rate' => 21,
        'cost_price' => 5.00
    ];

    echo "1. Ajout d'item...\n";
    $itemId = \App\Services\RegisterSessionService::addCartItem($item);
    echo "   ✓ Item ajouté avec ID: " . $itemId . "\n";

    // Ajouter l'ID à l'item pour le test
    $item['id'] = $itemId;

    echo "2. Test formatCartItem...\n";
    // Créer une instance du contrôleur pour tester formatCartItem
    $controller = new \App\Http\Controllers\Register\CartController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('formatCartItem');
    $method->setAccessible(true);
    
    $formattedItem = $method->invoke($controller, $item);
    echo "   ✓ Item formaté: " . json_encode($formattedItem) . "\n";

    echo "\n=== Test réussi ! ===\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
} 