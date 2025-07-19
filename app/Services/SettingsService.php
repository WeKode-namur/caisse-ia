<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\File;

class SettingsService
{
    private const SETTINGS_FILE = 'public/data/settings.json';

    /**
     * Met à jour un paramètre
     */
    public static function set(string $key, $value): bool
    {
        try {
            $settings = self::getAll();

            // Support pour les clés avec notation point
            if (strpos($key, '.') !== false) {
                $keys = explode('.', $key);
                $current = &$settings;

                foreach ($keys as $k) {
                    if (!isset($current[$k])) {
                        $current[$k] = [];
                    }
                    $current = &$current[$k];
                }

                $current = $value;
            } else {
                $settings[$key] = $value;
            }

            return self::save($settings);
        } catch (Exception $e) {
            // Utiliser error_log() au lieu de Log::error() pour éviter les problèmes de facade
            error_log("Erreur lors de la mise à jour du paramètre {$key}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les paramètres
     */
    public static function getAll(): array
    {
        try {
            if (!File::exists(self::SETTINGS_FILE)) {
                return self::getDefaultSettings();
            }

            $content = File::get(self::SETTINGS_FILE);
            $settings = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Ne pas utiliser Log::error() ici car Laravel n'est pas encore initialisé
                return self::getDefaultSettings();
            }

            return array_merge(self::getDefaultSettings(), $settings);
        } catch (Exception $e) {
            // Ne pas utiliser Log::error() ici car Laravel n'est pas encore initialisé
            return self::getDefaultSettings();
        }
    }

    /**
     * Retourne les paramètres par défaut
     */
    private static function getDefaultSettings(): array
    {
        return [
            'register' => [
                'tva_default' => null,
                'customer_management' => false,
                'arrondissement_method' => false
            ],
            'article' => [
                'seuil' => 5
            ],
            'generator' => [
                'barcode' => false
            ],
            'email' => [
                'active' => false
            ],
            'barcode' => [
                'prefix_one' => 'WK',
                'prefix_two' => 'NAM'
            ],
            'referent_lot_optionnel' => true,
            'date_expiration_optionnel' => true,
            'loyalty_point_step' => 1,
            'items' => [
                'sous_type' => false
            ],
            'suppliers_enabled' => false,
            'company' => [
                'address_street' => '',
                'address_postal' => '',
                'address_city' => '',
                'address_country' => '',
                'tva_number' => '',
                'phone' => ''
            ]
        ];
    }

    /**
     * Récupère un paramètre spécifique
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::getAll();

        // Support pour les clés avec notation point (ex: "register.tva_default")
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $value = $settings;

            foreach ($keys as $k) {
                if (!isset($value[$k])) {
                    return $default;
                }
                $value = $value[$k];
            }

            return $value;
        }

        return $settings[$key] ?? $default;
    }

    /**
     * Sauvegarde tous les paramètres
     */
    public static function save(array $settings): bool
    {
        try {
            $content = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('Erreur lors de l\'encodage des paramètres: ' . json_last_error_msg());
                return false;
            }

            // Vérifier si le dossier existe
            $directory = dirname(self::SETTINGS_FILE);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Vérifier les permissions et le chemin absolu
            $absolutePath = base_path(self::SETTINGS_FILE);
            error_log('DEBUG: Chemin absolu du fichier: ' . $absolutePath);

            if (File::exists(self::SETTINGS_FILE) && !is_writable(self::SETTINGS_FILE)) {
                error_log('Le fichier settings.json n\'est pas accessible en écriture: ' . self::SETTINGS_FILE);
                return false;
            }

            File::put(self::SETTINGS_FILE, $content);
            error_log('DEBUG: Fichier sauvegardé avec succès');
            return true;
        } catch (Exception $e) {
            error_log('Erreur lors de la sauvegarde du fichier settings.json: ' . $e->getMessage());
            error_log('Chemin du fichier: ' . self::SETTINGS_FILE);
            error_log('Dossier parent: ' . dirname(self::SETTINGS_FILE));
            return false;
        }
    }

    /**
     * Réinitialise les paramètres aux valeurs par défaut
     */
    public static function reset(): bool
    {
        return self::save(self::getDefaultSettings());
    }
}
