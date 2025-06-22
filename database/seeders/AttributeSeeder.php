<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // === TAILLE ===
        $taille = Attribute::create([
            'name' => 'Taille',
            'type' => 'select',
            'unit' => null
        ]);

        // Valeurs pour Taille
        $tailles = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        foreach ($tailles as $index => $t) {
            AttributeValue::create([
                'attribute_id' => $taille->id,
                'value' => $t,
                'second_value' => null,
                'order' => $index
            ]);
        }

        // === COULEUR ===
        $couleur = Attribute::create([
            'name' => 'Couleur',
            'type' => 'color',
            'unit' => null
        ]);

        // Valeurs pour Couleur
        $couleurs = [
            ['Rouge', '#FF0000'],
            ['Bleu', '#0000FF'],
            ['Vert', '#00FF00'],
            ['Noir', '#000000'],
            ['Blanc', '#FFFFFF'],
            ['Gris', '#808080'],
            ['Jaune', '#FFFF00'],
            ['Rose', '#FFC0CB'],
            ['Violet', '#800080'],
            ['Orange', '#FFA500'],
            ['Marron', '#8B4513'],
            ['Beige', '#F5F5DC']
        ];

        foreach ($couleurs as $index => $c) {
            AttributeValue::create([
                'attribute_id' => $couleur->id,
                'value' => $c[0],
                'second_value' => $c[1], // Code couleur hex
                'order' => $index
            ]);
        }

        // === POINTURE ===
        $pointure = Attribute::create([
            'name' => 'Pointure',
            'type' => 'select',
            'unit' => 'EU'
        ]);

        // Valeurs pour Pointure
        $pointures = ['35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47'];
        foreach ($pointures as $index => $p) {
            AttributeValue::create([
                'attribute_id' => $pointure->id,
                'value' => $p,
                'second_value' => null,
                'order' => $index
            ]);
        }

        // === MATIÈRE ===
        $matiere = Attribute::create([
            'name' => 'Matière',
            'type' => 'select',
            'unit' => null
        ]);

        // Valeurs pour Matière
        $matieres = [
            'Coton 100%',
            'Polyester',
            'Coton/Polyester',
            'Laine',
            'Cuir',
            'Cuir synthétique',
            'Jean',
            'Lin',
            'Soie',
            'Cachemire',
            'Lycra/Élasthanne'
        ];

        foreach ($matieres as $index => $m) {
            AttributeValue::create([
                'attribute_id' => $matiere->id,
                'value' => $m,
                'second_value' => null,
                'order' => $index
            ]);
        }

        // === MARQUE ===
        $marque = Attribute::create([
            'name' => 'Marque',
            'type' => 'select',
            'unit' => null
        ]);

        // Valeurs pour Marque
        $marques = [
            'Nike',
            'Adidas',
            'Zara',
            'H&M',
            'Uniqlo',
            'Levi\'s',
            'Tommy Hilfiger',
            'Calvin Klein',
            'Hugo Boss',
            'Apple',
            'Samsung',
            'Sony',
            'Propriétaire' // Pour les articles sans marque spécifique
        ];

        foreach ($marques as $index => $m) {
            AttributeValue::create([
                'attribute_id' => $marque->id,
                'value' => $m,
                'second_value' => null,
                'order' => $index
            ]);
        }

        // === STOCKAGE (pour électronique) ===
        $stockage = Attribute::create([
            'name' => 'Stockage',
            'type' => 'select',
            'unit' => 'GB'
        ]);

        // Valeurs pour Stockage
        $stockages = ['64', '128', '256', '512', '1000'];
        foreach ($stockages as $index => $s) {
            AttributeValue::create([
                'attribute_id' => $stockage->id,
                'value' => $s,
                'second_value' => null,
                'order' => $index
            ]);
        }

        // === ÉTAT ===
        $etat = Attribute::create([
            'name' => 'État',
            'type' => 'select',
            'unit' => null
        ]);

        // Valeurs pour État
        $etats = [
            'Neuf',
            'Comme neuf',
            'Très bon état',
            'Bon état',
            'État correct',
            'Reconditionné'
        ];

        foreach ($etats as $index => $e) {
            AttributeValue::create([
                'attribute_id' => $etat->id,
                'value' => $e,
                'second_value' => null,
                'order' => $index
            ]);
        }
    }
}
