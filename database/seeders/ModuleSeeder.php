<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider la table modules avant de la remplir
        Module::truncate();

        // Modules principaux
        $modules = [
            [
                'name' => 'dashboard',
                'display_name' => 'Dashboard',
                'description' => 'Tableau de bord principal',
                'icon' => 'fas fa-tachometer-alt',
                'route_prefix' => 'dashboard',
                'is_enabled' => true,
                'is_visible_sidebar' => true,
                'sort_order' => 1,
                'min_admin_level' => 0,
            ],
                [
                    'name' => 'register',
                    'display_name' => 'Caisse',
                    'description' => 'Interface de caisse',
                    'icon' => 'fas fa-cash-register',
                    'route_prefix' => 'register.index',
                    'is_enabled' => true,
                    'is_visible_sidebar' => true,
                    'sort_order' => 1,
                    'min_admin_level' => 0,
                ],
                [
                    'name' => 'transactions',
                    'display_name' => 'Tickets & Factures',
                    'description' => 'Liste des transactions',
                    'icon' => 'fas fa-receipt',
                    'route_prefix' => 'transactions,tickets,factures',
                    'is_enabled' => true,
                    'is_visible_sidebar' => true,
                    'sort_order' => 2,
                    'min_admin_level' => 0,
                ],
            [
                'name' => 'inventory',
                'display_name' => 'Inventaire',
                'description' => 'Gestion des articles et stocks',
                'icon' => 'fas fa-boxes',
                'route_prefix' => 'inventory',
                'is_enabled' => true,
                'is_visible_sidebar' => true,
                'sort_order' => 3,
                'min_admin_level' => 0,
            ],
            [
                'name' => 'clients',
                'display_name' => 'Clients',
                'description' => 'Gestion de la clientèle',
                'icon' => 'fas fa-users',
                'route_prefix' => 'clients',
                'is_enabled' => true,
                'is_visible_sidebar' => true,
                'sort_order' => 4,
                'min_admin_level' => 90,
            ],
            [
                'name' => 'statistics',
                'display_name' => 'Statistiques',
                'description' => 'Rapports et analyses',
                'icon' => 'fas fa-chart-bar',
                'route_prefix' => 'statistics',
                'is_enabled' => true,
                'is_visible_sidebar' => true,
                'sort_order' => 5,
                'min_admin_level' => 90,
            ],
            [
                'name' => 'closure',
                'display_name' => 'Clôture journalière',
                'description' => 'Clôture de caisse',
                'icon' => 'fas fa-lock',
                'route_prefix' => 'closure',
                'is_enabled' => true,
                'is_visible_sidebar' => true,
                'sort_order' => 6,
                'min_admin_level' => 90,
            ],
            [
                'name' => 'wix',
                'display_name' => 'Sorties Wix',
                'description' => 'Gestion des sorties Wix',
                'icon' => 'fab fa-wix',
                'route_prefix' => 'wix',
                'is_enabled' => true,
                'is_visible_sidebar' => true,
                'sort_order' => 7,
                'min_admin_level' => 90,
            ],
            [
                'name' => 'users',
                'display_name' => 'Utilisateurs',
                'description' => 'Gestion des utilisateurs',
                'icon' => 'fas fa-users-cog',
                'route_prefix' => 'users',
                'is_enabled' => true,
                'is_visible_sidebar' => false,
                'sort_order' => 8,
                'min_admin_level' => 90,
            ],
            [
                'name' => 'logs',
                'display_name' => 'Logs système',
                'description' => 'Journal des actions système',
                'icon' => 'fas fa-file-alt',
                'route_prefix' => 'logs',
                'is_enabled' => true,
                'is_visible_sidebar' => true,
                'sort_order' => 9,
                'min_admin_level' => 100,
            ],
            [
                'name' => 'settings',
                'display_name' => 'Paramètres',
                'description' => 'Configuration système',
                'icon' => 'fas fa-cog',
                'route_prefix' => 'settings',
                'is_enabled' => true,
                'is_visible_sidebar' => false,
                'sort_order' => 10,
                'min_admin_level' => 90,
            ],
            [
                'name' => 'support',
                'display_name' => 'Support',
                'description' => 'Support technique',
                'icon' => 'fas fa-life-ring',
                'route_prefix' => 'support',
                'is_enabled' => true,
                'is_visible_sidebar' => false,
                'sort_order' => 11,
                'min_admin_level' => 90,
            ],
        ];

        foreach ($modules as $moduleData) {
            Module::create($moduleData);
        }


        // Sous-modules pour Inventaire
        $inventoryModule = Module::where('name', 'inventory')->first();
        if ($inventoryModule) {
            $inventorySubModules = [
                [
                    'name' => 'inventory.list',
                    'display_name' => 'Liste des articles',
                    'description' => 'Voir tous les articles',
                    'icon' => 'fas fa-list',
                    'route_prefix' => 'inventory.index',
                    'is_enabled' => true,
                    'is_visible_sidebar' => true,
                    'sort_order' => 1,
                    'min_admin_level' => 0,
                    'parent_module_id' => $inventoryModule->id,
                ],
                [
                    'name' => 'inventory.create',
                    'display_name' => 'Ajouter un article',
                    'description' => 'Créer un nouvel article',
                    'icon' => 'fas fa-plus',
                    'route_prefix' => 'inventory.create.index',
                    'is_enabled' => true,
                    'is_visible_sidebar' => true,
                    'sort_order' => 2,
                    'min_admin_level' => 0,
                    'parent_module_id' => $inventoryModule->id,
                ],
            ];

            foreach ($inventorySubModules as $subModuleData) {
                Module::create($subModuleData);
            }
        }

        // Sous-modules pour Clients
        $clientsModule = Module::where('name', 'clients')->first();
        if ($clientsModule) {
            $clientsSubModules = [
                [
                    'name' => 'clients.list',
                    'display_name' => 'Liste des clients',
                    'description' => 'Voir tous les clients',
                    'icon' => 'fas fa-list',
                    'route_prefix' => 'clients.index',
                    'is_enabled' => true,
                    'is_visible_sidebar' => true,
                    'sort_order' => 1,
                    'min_admin_level' => 90,
                    'parent_module_id' => $clientsModule->id,
                ],
                [
                    'name' => 'clients.create',
                    'display_name' => 'Nouveau client',
                    'description' => 'Créer un nouveau client',
                    'icon' => 'fas fa-user-plus',
                    'route_prefix' => 'clients.create',
                    'is_enabled' => true,
                    'is_visible_sidebar' => true,
                    'sort_order' => 2,
                    'min_admin_level' => 90,
                    'parent_module_id' => $clientsModule->id,
                ],
            ];

            foreach ($clientsSubModules as $subModuleData) {
                Module::create($subModuleData);
            }
        }
    }
}
