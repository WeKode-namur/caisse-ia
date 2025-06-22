<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Variant;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Type;
use App\Models\Subtype;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupération des données de référence
        $categories = Category::all()->keyBy('name');
        $attributes = Attribute::all()->keyBy('name');

        // === T-SHIRT HOMME ===
        $tshirtHomme = Article::create([
            'name' => 'T-shirt basique homme',
            'description' => 'T-shirt en coton 100% pour homme, coupe classique',
            'category_id' => $categories['Vêtements']->id,
            'type_id' => Type::where('name', 'Hommes')->first()->id,
            'subtype_id' => Subtype::where('name', 'T-shirts')->first()->id,
            'sell_price' => '19.99',
            'buy_price' => '8.50',
            'tva' => 21
        ]);

        // Créer des variants avec différentes tailles et couleurs
        $tailles = AttributeValue::where('attribute_id', $attributes['Taille']->id)->get();
        $couleurs = AttributeValue::where('attribute_id', $attributes['Couleur']->id)->whereIn('value', ['Blanc', 'Noir', 'Bleu'])->get();
        $coton = AttributeValue::where('attribute_id', $attributes['Matière']->id)->where('value', 'Coton 100%')->first();

        foreach ($couleurs as $couleur) {
            foreach ($tailles->take(5) as $taille) { // S à XL
                $variant = Variant::create([
                    'article_id' => $tshirtHomme->id,
                    'barcode' => '300' . str_pad($couleur->id, 2, '0', STR_PAD_LEFT) . str_pad($taille->id, 2, '0', STR_PAD_LEFT) . '01',
                    'reference' => 'TSH-H-' . strtoupper(substr($couleur->value, 0, 3)) . '-' . $taille->value,
                    'sell_price' => null, // Utilise le prix de l'article
                    'buy_price' => null
                ]);

                // Associer les attributs
                $variant->attributeValues()->attach([$taille->id, $couleur->id, $coton->id]);

                // Créer du stock
                Stock::create([
                    'variant_id' => $variant->id,
                    'buy_price' => 8.50,
                    'quantity' => rand(10, 50),
                    'lot_reference' => 'LOT-2024-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'expiry_date' => null
                ]);
            }
        }

        // === JEAN FEMME ===
        $jeanFemme = Article::create([
            'name' => 'Jean slim femme',
            'description' => 'Jean slim taille haute pour femme',
            'category_id' => $categories['Vêtements']->id,
            'type_id' => Type::where('name', 'Femmes')->first()->id,
            'subtype_id' => Subtype::where('name', 'Pantalons')->first()->id,
            'sell_price' => '49.99',
            'buy_price' => '22.00',
            'tva' => 21
        ]);

        $jean = AttributeValue::where('attribute_id', $attributes['Matière']->id)->where('value', 'Jean')->first();
        $couleursJean = AttributeValue::where('attribute_id', $attributes['Couleur']->id)->whereIn('value', ['Bleu', 'Noir'])->get();

        foreach ($couleursJean as $couleur) {
            foreach ($tailles->slice(1, 4) as $taille) { // S à L
                $variant = Variant::create([
                    'article_id' => $jeanFemme->id,
                    'barcode' => '301' . str_pad($couleur->id, 2, '0', STR_PAD_LEFT) . str_pad($taille->id, 2, '0', STR_PAD_LEFT) . '01',
                    'reference' => 'JEAN-F-' . strtoupper(substr($couleur->value, 0, 3)) . '-' . $taille->value,
                ]);

                $variant->attributeValues()->attach([$taille->id, $couleur->id, $jean->id]);

                Stock::create([
                    'variant_id' => $variant->id,
                    'buy_price' => 22.00,
                    'quantity' => rand(5, 25),
                    'lot_reference' => 'LOT-2024-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // === CHAUSSURES RUNNING ===
        $running = Article::create([
            'name' => 'Chaussures de running',
            'description' => 'Chaussures de course unisexe avec semelle amortissante',
            'category_id' => $categories['Chaussures']->id,
            'type_id' => Type::where('name', 'Sport')->first()->id,
            'subtype_id' => Subtype::where('name', 'Running')->first()->id,
            'sell_price' => '89.99',
            'buy_price' => '45.00',
            'tva' => 21
        ]);

        $pointures = AttributeValue::where('attribute_id', $attributes['Pointure']->id)->get();
        $nike = AttributeValue::where('attribute_id', $attributes['Marque']->id)->where('value', 'Nike')->first();
        $couleursRunning = AttributeValue::where('attribute_id', $attributes['Couleur']->id)->whereIn('value', ['Blanc', 'Noir', 'Rouge'])->get();

        foreach ($couleursRunning as $couleur) {
            foreach ($pointures->slice(4, 8) as $pointure) { // 39 à 46
                $variant = Variant::create([
                    'article_id' => $running->id,
                    'barcode' => '400' . str_pad($couleur->id, 2, '0', STR_PAD_LEFT) . str_pad($pointure->id, 2, '0', STR_PAD_LEFT) . '01',
                    'reference' => 'RUN-' . strtoupper(substr($couleur->value, 0, 3)) . '-' . $pointure->value,
                ]);

                $variant->attributeValues()->attach([$pointure->id, $couleur->id, $nike->id]);

                Stock::create([
                    'variant_id' => $variant->id,
                    'buy_price' => 45.00,
                    'quantity' => rand(3, 15),
                    'lot_reference' => 'LOT-2024-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // === SMARTPHONE IPHONE ===
        $iphone = Article::create([
            'name' => 'iPhone 15',
            'description' => 'Smartphone Apple iPhone 15 dernière génération',
            'category_id' => $categories['Électronique']->id,
            'type_id' => Type::where('name', 'Smartphones')->first()->id,
            'subtype_id' => Subtype::where('name', 'iPhone')->first()->id,
            'sell_price' => '899.99',
            'buy_price' => '650.00',
            'tva' => 21
        ]);

        $apple = AttributeValue::where('attribute_id', $attributes['Marque']->id)->where('value', 'Apple')->first();
        $stockages = AttributeValue::where('attribute_id', $attributes['Stockage']->id)->whereIn('value', ['128', '256', '512'])->get();
        $etatNeuf = AttributeValue::where('attribute_id', $attributes['État']->id)->where('value', 'Neuf')->first();

        foreach ($stockages as $stockage) {
            $variant = Variant::create([
                'article_id' => $iphone->id,
                'barcode' => '500' . str_pad($stockage->id, 2, '0', STR_PAD_LEFT) . '0001',
                'reference' => 'IPH15-' . $stockage->value . 'GB',
                'sell_price' => $stockage->value == '128' ? null : ($stockage->value == '256' ? '999.99' : '1199.99'),
            ]);

            if ($apple) $variant->attributeValues()->attach($apple->id);
            $variant->attributeValues()->attach([$stockage->id, $etatNeuf->id]);

            Stock::create([
                'variant_id' => $variant->id,
                'buy_price' => $stockage->value == '128' ? 650.00 : ($stockage->value == '256' ? 750.00 : 850.00),
                'quantity' => rand(2, 8),
                'lot_reference' => 'LOT-2024-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            ]);
        }

        // === MONTRE ===
        $montre = Article::create([
            'name' => 'Montre classique',
            'description' => 'Montre analogique classique avec bracelet cuir',
            'category_id' => $categories['Accessoires']->id,
            'type_id' => Type::where('name', 'Bijouterie')->first()->id,
            'subtype_id' => Subtype::where('name', 'Montres')->first()->id,
            'sell_price' => '129.99',
            'buy_price' => '65.00',
            'tva' => 21
        ]);

        $cuir = AttributeValue::where('attribute_id', $attributes['Matière']->id)->where('value', 'Cuir')->first();
        $couleursMontres = AttributeValue::where('attribute_id', $attributes['Couleur']->id)->whereIn('value', ['Noir', 'Marron'])->get();

        foreach ($couleursMontres as $couleur) {
            $variant = Variant::create([
                'article_id' => $montre->id,
                'barcode' => '600' . str_pad($couleur->id, 2, '0', STR_PAD_LEFT) . '0001',
                'reference' => 'MON-' . strtoupper(substr($couleur->value, 0, 3)),
            ]);

            $variant->attributeValues()->attach([$couleur->id, $cuir->id, $etatNeuf->id]);

            Stock::create([
                'variant_id' => $variant->id,
                'buy_price' => 65.00,
                'quantity' => rand(5, 20),
                'lot_reference' => 'LOT-2024-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
