<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Type;
use App\Models\Subtype;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // === VÊTEMENTS ===
        $vetements = Category::create([
            'name' => 'Vêtements',
            'description' => 'Tous les articles d\'habillement'
        ]);

        // Types pour Vêtements
        $hommesType = Type::create([
            'name' => 'Hommes',
            'description' => 'Vêtements pour hommes',
            'category_id' => $vetements->id
        ]);

        $femmesType = Type::create([
            'name' => 'Femmes',
            'description' => 'Vêtements pour femmes',
            'category_id' => $vetements->id
        ]);

        $enfantsType = Type::create([
            'name' => 'Enfants',
            'description' => 'Vêtements pour enfants',
            'category_id' => $vetements->id
        ]);

        // Sous-types pour Hommes
        Subtype::create(['name' => 'T-shirts', 'description' => 'T-shirts hommes', 'type_id' => $hommesType->id]);
        Subtype::create(['name' => 'Pantalons', 'description' => 'Pantalons hommes', 'type_id' => $hommesType->id]);
        Subtype::create(['name' => 'Chemises', 'description' => 'Chemises hommes', 'type_id' => $hommesType->id]);
        Subtype::create(['name' => 'Pulls', 'description' => 'Pulls hommes', 'type_id' => $hommesType->id]);

        // Sous-types pour Femmes
        Subtype::create(['name' => 'Robes', 'description' => 'Robes femmes', 'type_id' => $femmesType->id]);
        Subtype::create(['name' => 'Blouses', 'description' => 'Blouses femmes', 'type_id' => $femmesType->id]);
        Subtype::create(['name' => 'Jupes', 'description' => 'Jupes femmes', 'type_id' => $femmesType->id]);
        Subtype::create(['name' => 'Pantalons', 'description' => 'Pantalons femmes', 'type_id' => $femmesType->id]);

        // Sous-types pour Enfants
        Subtype::create(['name' => 'Bodies', 'description' => 'Bodies bébés', 'type_id' => $enfantsType->id]);
        Subtype::create(['name' => 'T-shirts', 'description' => 'T-shirts enfants', 'type_id' => $enfantsType->id]);

        // === CHAUSSURES ===
        $chaussures = Category::create([
            'name' => 'Chaussures',
            'description' => 'Toutes les chaussures'
        ]);

        // Types pour Chaussures
        $sportType = Type::create([
            'name' => 'Sport',
            'description' => 'Chaussures de sport',
            'category_id' => $chaussures->id
        ]);

        $villeType = Type::create([
            'name' => 'Ville',
            'description' => 'Chaussures de ville',
            'category_id' => $chaussures->id
        ]);

        // Sous-types pour Sport
        Subtype::create(['name' => 'Running', 'description' => 'Chaussures de course', 'type_id' => $sportType->id]);
        Subtype::create(['name' => 'Basketball', 'description' => 'Chaussures de basket', 'type_id' => $sportType->id]);
        Subtype::create(['name' => 'Football', 'description' => 'Chaussures de foot', 'type_id' => $sportType->id]);

        // Sous-types pour Ville
        Subtype::create(['name' => 'Mocassins', 'description' => 'Mocassins classiques', 'type_id' => $villeType->id]);
        Subtype::create(['name' => 'Escarpins', 'description' => 'Escarpins femmes', 'type_id' => $villeType->id]);
        Subtype::create(['name' => 'Bottines', 'description' => 'Bottines mode', 'type_id' => $villeType->id]);

        // === ACCESSOIRES ===
        $accessoires = Category::create([
            'name' => 'Accessoires',
            'description' => 'Accessoires de mode'
        ]);

        // Types pour Accessoires
        $bijouterieType = Type::create([
            'name' => 'Bijouterie',
            'description' => 'Bijoux et montres',
            'category_id' => $accessoires->id
        ]);

        $maroquinerieType = Type::create([
            'name' => 'Maroquinerie',
            'description' => 'Sacs et portefeuilles',
            'category_id' => $accessoires->id
        ]);

        // Sous-types pour Bijouterie
        Subtype::create(['name' => 'Montres', 'description' => 'Montres diverses', 'type_id' => $bijouterieType->id]);
        Subtype::create(['name' => 'Colliers', 'description' => 'Colliers fantaisie', 'type_id' => $bijouterieType->id]);
        Subtype::create(['name' => 'Bagues', 'description' => 'Bagues diverses', 'type_id' => $bijouterieType->id]);

        // Sous-types pour Maroquinerie
        Subtype::create(['name' => 'Sacs à main', 'description' => 'Sacs à main femmes', 'type_id' => $maroquinerieType->id]);
        Subtype::create(['name' => 'Portefeuilles', 'description' => 'Portefeuilles diverses', 'type_id' => $maroquinerieType->id]);
        Subtype::create(['name' => 'Sacs à dos', 'description' => 'Sacs à dos casual', 'type_id' => $maroquinerieType->id]);

        // === ÉLECTRONIQUE ===
        $electronique = Category::create([
            'name' => 'Électronique',
            'description' => 'Appareils électroniques'
        ]);

        // Types pour Électronique
        $smartphoneType = Type::create([
            'name' => 'Smartphones',
            'description' => 'Téléphones intelligents',
            'category_id' => $electronique->id
        ]);

        $accessoiresElecType = Type::create([
            'name' => 'Accessoires',
            'description' => 'Accessoires électroniques',
            'category_id' => $electronique->id
        ]);

        // Sous-types pour Smartphones
        Subtype::create(['name' => 'iPhone', 'description' => 'iPhones Apple', 'type_id' => $smartphoneType->id]);
        Subtype::create(['name' => 'Samsung', 'description' => 'Samsung Galaxy', 'type_id' => $smartphoneType->id]);
        Subtype::create(['name' => 'Autres', 'description' => 'Autres marques', 'type_id' => $smartphoneType->id]);

        // Sous-types pour Accessoires Électronique
        Subtype::create(['name' => 'Coques', 'description' => 'Coques de protection', 'type_id' => $accessoiresElecType->id]);
        Subtype::create(['name' => 'Chargeurs', 'description' => 'Chargeurs divers', 'type_id' => $accessoiresElecType->id]);
        Subtype::create(['name' => 'Écouteurs', 'description' => 'Écouteurs et casques', 'type_id' => $accessoiresElecType->id]);
    }
}
