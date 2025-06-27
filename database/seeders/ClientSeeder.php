<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Créer 20 clients particuliers
        for ($i = 0; $i < 20; $i++) {
            Customer::create([
                'gender' => $faker->randomElement(['M', 'F']),
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'birth_date' => $faker->dateTimeBetween('-80 years', '-18 years'),
                'loyalty_points' => $faker->numberBetween(0, 5000),
                'total_purchases' => $faker->randomFloat(2, 0, 15000),
                'marketing_consent' => $faker->boolean(70),
                'notes' => $faker->optional(0.3)->sentence(),
                'is_active' => $faker->boolean(90),
            ]);
        }

        // Créer 10 entreprises
        $companyTypes = ['SPRL', 'SA', 'SRL', 'SNC', 'SC', 'ASBL', 'AUTRE'];
        
        for ($i = 0; $i < 10; $i++) {
            Company::create([
                'name' => $faker->company(),
                'legal_name' => $faker->optional(0.7)->company(),
                'company_number_be' => $faker->optional(0.8)->numerify('##########'),
                'vat_number' => $faker->optional(0.6)->numerify('BE##########'),
                'company_type' => $faker->randomElement($companyTypes),
                'legal_representative' => $faker->optional(0.8)->name(),
                'email' => $faker->optional(0.9)->companyEmail(),
                'phone' => $faker->optional(0.8)->phoneNumber(),
                'payment_terms' => $faker->randomElement([0, 15, 30, 45, 60]),
                'credit_limit' => $faker->randomFloat(2, 0, 50000),
                'loyalty_points' => $faker->numberBetween(0, 10000),
                'total_purchases' => $faker->randomFloat(2, 0, 100000),
                'notes' => $faker->optional(0.4)->sentence(),
                'is_active' => $faker->boolean(85),
            ]);
        }
    }
} 