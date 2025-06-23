<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Désactiver les contraintes de clé étrangère pour le nettoyage
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PaymentMethod::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $methods = [
            [
                'name' => 'Espèces',
                'code' => 'cash',
                'icon' => 'money-bill-wave',
                'is_active' => true,
                'order_column' => 1
            ],
            [
                'name' => 'Bancontact',
                'code' => 'bancontact',
                'icon' => 'credit-card',
                'is_active' => true,
                'order_column' => 2
            ],
            [
                'name' => 'Carte de crédit',
                'code' => 'credit_card',
                'icon' => 'credit-card',
                'is_active' => true,
                'order_column' => 3
            ],
            [
                'name' => 'Virement bancaire',
                'code' => 'bank_transfer',
                'icon' => 'university',
                'is_active' => false, // Souvent pas utilisé en caisse
                'order_column' => 4
            ],
            [
                'name' => 'Carte Cadeau',
                'code' => 'gift_card',
                'icon' => 'gift',
                'is_active' => false,
                'order_column' => 5
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
} 