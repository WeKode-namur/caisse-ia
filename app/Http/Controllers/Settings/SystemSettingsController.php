<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class SystemSettingsController extends Controller
{
    /**
     * Affiche la page des paramètres système
     */
    public function index()
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        $userLevel = Auth::user()->is_admin;

        // Utiliser directement config('custom') qui gère déjà la hiérarchie .env -> JSON
        $settings = $this->formatSettingsForView(config('custom'));

        return view('panel.settings.system.index', compact('settings', 'userLevel'));
    }

    /**
     * Vérifie les permissions d'administrateur
     */
    private function checkAdminPermissions()
    {
        if (Auth::user()->is_admin < 80) {
            return redirect()->route('settings.index')->with('error', 'Accès refusé. Niveau d\'administrateur insuffisant.');
        }
        return null;
    }

    /**
     * Formate les paramètres de config('custom') pour la vue
     */
    private function formatSettingsForView($config)
    {
        return [
            'generator' => [
                'barcode' => $config['generator']['barcode'] ?? false,
            ],
            'article' => [
                'seuil' => $config['article']['seuil'] ?? 5,
            ],
            'register' => [
                'customer_management' => $config['register']['customer_management'] ?? false,
                'arrondissement_method' => $config['register']['arrondissementMethod'] ?? false,
                'tva_default' => $config['register']['tva_default'] ?? null,
            ],
            'email' => [
                'active' => $config['email']['active'] ?? false,
            ],
            'barcode' => [
                'prefix_one' => $config['barcode']['prefix_one'] ?? 'WK',
                'prefix_two' => $config['barcode']['prefix_two'] ?? 'NAM',
            ],
            'referent_lot_optionnel' => $config['referent_lot_optionnel'] ?? true,
            'date_expiration_optionnel' => $config['date_expiration_optionnel'] ?? true,
            'company' => [
                'address_street' => $config['address']['street'] ?? '',
                'address_postal' => $config['address']['postal'] ?? '',
                'address_city' => $config['address']['city'] ?? '',
                'address_country' => $config['address']['country'] ?? '',
                'tva_number' => $config['tva'] ?? '',
                'phone' => $config['phone'] ?? '',
            ],
            'loyalty_point_step' => $config['loyalty_point_step'] ?? 1,
            'items' => [
                'sous_type' => $config['items']['sousType'] ?? false,
            ],
            'suppliers_enabled' => $config['suppliers_enabled'] ?? false,
        ];
    }

    /**
     * Met à jour les paramètres système
     */
    public function update(Request $request)
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        $userLevel = Auth::user()->is_admin;
        $oldSettings = $this->formatSettingsForView(config('custom'));
        $changes = [];

        // Validation selon le niveau d'utilisateur
        $validationRules = $this->getValidationRules($userLevel);
        $request->validate($validationRules['rules'], $validationRules['messages']);

        try {
            $success = true;
            $errors = [];

            // Traiter les paramètres selon le niveau d'utilisateur
            $allowedFields = $this->getAllowedFields($userLevel);

            foreach ($allowedFields as $field) {
                $value = $this->getFieldValue($request, $field);

                // Vérifier si la valeur a changé
                $oldValue = $this->getNestedValue($oldSettings, $field);

                // Comparaison intelligente selon le type de champ
                $hasChanged = $this->hasValueChanged($field, $oldValue, $value);

                if ($hasChanged) {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $value
                    ];
                    Log::info("Change detected for {$field}");
                    // NE PAS sauvegarder ici, attendre la confirmation
                } else {
                    Log::info("No change detected for {$field}, skipping save");
                }
            }

            if ($success) {
                if (empty($changes)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Aucune modification détectée.',
                        'changes' => []
                    ]);
                }

                // Stocker les changements en session pour le modal de confirmation
                session(['settings_changes' => $changes]);

                return response()->json([
                    'success' => true,
                    'message' => 'Modifications détectées.',
                    'changes' => $changes
                ]);
            } else {
                return redirect()->route('settings.system.index')
                    ->withErrors($errors)
                    ->withInput();
            }
        } catch (Exception $e) {
            $errorMessage = 'Erreur lors de la sauvegarde des paramètres.';

            // Messages d'erreur plus spécifiques selon le type d'erreur
            if (strpos($e->getMessage(), 'permissions') !== false || strpos($e->getMessage(), 'accessible en écriture') !== false) {
                $errorMessage = 'Erreur de permissions : Le fichier de configuration n\'est pas accessible en écriture. Vérifiez les permissions du dossier public/data/.';
            } elseif (strpos($e->getMessage(), 'encodage') !== false) {
                $errorMessage = 'Erreur de format : Impossible d\'encoder les paramètres en JSON.';
            } elseif (strpos($e->getMessage(), 'makeDirectory') !== false) {
                $errorMessage = 'Erreur de création : Impossible de créer le dossier de configuration.';
            } else {
                $errorMessage .= ' ' . $e->getMessage();
            }

            return redirect()->route('settings.system.index')
                ->withErrors(['error' => $errorMessage])
                ->withInput();
        }
    }

    /**
     * Retourne les règles de validation selon le niveau d'utilisateur
     */
    private function getValidationRules($userLevel)
    {
        $baseRules = [
            'loyalty_point_step' => 'required|integer|min:1|max:100',
        ];

        $baseMessages = [
            'loyalty_point_step.required' => 'Le pas des points de fidélité est obligatoire.',
            'loyalty_point_step.integer' => 'Le pas des points de fidélité doit être un nombre entier.',
            'loyalty_point_step.min' => 'Le pas des points de fidélité doit être au moins 1.',
            'loyalty_point_step.max' => 'Le pas des points de fidélité ne peut pas dépasser 100.',
        ];

        if ($userLevel >= 100) {
            $baseRules = array_merge($baseRules, [
                'register.tva_default' => 'nullable|integer|in:0,6,12,21',
                'register.customer_management' => 'boolean',
                'register.arrondissement_method' => 'boolean',
                'article.seuil' => 'required|integer|min:0|max:1000',
                'generator.barcode' => 'boolean',
                'email.active' => 'boolean',
                'barcode.prefix_one' => 'required|string|max:10',
                'barcode.prefix_two' => 'required|string|max:10',
                'referent_lot_optionnel' => 'boolean',
                'date_expiration_optionnel' => 'boolean',
                'items.sous_type' => 'boolean',
                'suppliers_enabled' => 'boolean',
                'company.address_street' => 'nullable|string|max:255',
                'company.address_postal' => 'nullable|string|max:20',
                'company.address_city' => 'nullable|string|max:100',
                'company.address_country' => 'nullable|string|max:100',
                'company.tva_number' => 'nullable|string|max:50',
                'company.phone' => 'nullable|string|max:20',
            ]);

            $baseMessages = array_merge($baseMessages, [
                'register.tva_default.in' => 'La TVA par défaut doit être 0%, 6%, 12% ou 21%.',
                'article.seuil.required' => 'Le seuil d\'alerte est obligatoire.',
                'article.seuil.integer' => 'Le seuil d\'alerte doit être un nombre entier.',
                'article.seuil.min' => 'Le seuil d\'alerte ne peut pas être négatif.',
                'article.seuil.max' => 'Le seuil d\'alerte ne peut pas dépasser 1000.',
                'barcode.prefix_one.required' => 'Le préfixe 1 est obligatoire.',
                'barcode.prefix_one.max' => 'Le préfixe 1 ne peut pas dépasser 10 caractères.',
                'barcode.prefix_two.required' => 'Le préfixe 2 est obligatoire.',
                'barcode.prefix_two.max' => 'Le préfixe 2 ne peut pas dépasser 10 caractères.',
            ]);
        } else {
            // Niveau 80 : TVA par défaut, points de fidélité et coordonnées entreprise
            $baseRules = array_merge($baseRules, [
                'register.tva_default' => 'nullable|integer|in:0,6,12,21',
                'company.address_street' => 'nullable|string|max:255',
                'company.address_postal' => 'nullable|string|max:20',
                'company.address_city' => 'nullable|string|max:100',
                'company.address_country' => 'nullable|string|max:100',
                'company.tva_number' => 'nullable|string|max:50',
                'company.phone' => 'nullable|string|max:20',
            ]);

            $baseMessages = array_merge($baseMessages, [
                'register.tva_default.in' => 'La TVA par défaut doit être 0%, 6%, 12% ou 21%.',
            ]);
        }

        return [
            'rules' => $baseRules,
            'messages' => $baseMessages
        ];
    }

    /**
     * Retourne les champs autorisés selon le niveau d'utilisateur
     */
    private function getAllowedFields($userLevel)
    {
        if ($userLevel >= 100) {
            return [
                'register.tva_default',
                'register.customer_management',
                'register.arrondissement_method',
                'article.seuil',
                'generator.barcode',
                'email.active',
                'barcode.prefix_one',
                'barcode.prefix_two',
                'referent_lot_optionnel',
                'date_expiration_optionnel',
                'loyalty_point_step',
                'items.sous_type',
                'suppliers_enabled',
                'company.address_street',
                'company.address_postal',
                'company.address_city',
                'company.address_country',
                'company.tva_number',
                'company.phone',
            ];
        } else {
            // Niveau 80 : TVA par défaut, points de fidélité et coordonnées entreprise
            return [
                'register.tva_default',
                'loyalty_point_step',
                'company.address_street',
                'company.address_postal',
                'company.address_city',
                'company.address_country',
                'company.tva_number',
                'company.phone',
            ];
        }
    }

    /**
     * Récupère la valeur d'un champ selon son type
     */
    private function getFieldValue($request, $field)
    {
        // Liste des champs booléens
        $booleanFields = [
            'register.customer_management',
            'register.arrondissement_method',
            'generator.barcode',
            'email.active',
            'referent_lot_optionnel',
            'date_expiration_optionnel',
            'items.sous_type',
            'suppliers_enabled'
        ];

        if (in_array($field, $booleanFields)) {
            return $request->has($field) ? true : false;
        }

        $value = $request->input($field);

        // Traitement spécial pour la TVA par défaut
        if ($field === 'register.tva_default') {
            // Si la valeur est vide ou null, retourner null
            if (empty($value) && $value !== '0') {
                return null;
            }
            // Convertir en entier
            return (int)$value;
        }

        return $value;
    }

    /**
     * Récupère une valeur imbriquée dans un tableau
     */
    private function getNestedValue($array, $key)
    {
        $keys = explode('.', $key);
        $value = $array;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Vérifie si une valeur a changé en tenant compte du type de champ
     */
    private function hasValueChanged($field, $oldValue, $newValue)
    {
        // Normaliser les valeurs pour la comparaison
        $oldValue = $this->normalizeValue($oldValue);
        $newValue = $this->normalizeValue($newValue);

        // Debug visible
        $debugMsg = "DEBUG: {$field} - Old: '" . var_export($oldValue, true) . "' New: '" . var_export($newValue, true) . "'";
        error_log($debugMsg);

        // Pour la TVA par défaut, comparer en tant qu'entiers
        if ($field === 'register.tva_default') {
            $oldInt = $oldValue === null ? null : (int)$oldValue;
            $newInt = $newValue === null ? null : (int)$newValue;

            $changed = $oldInt !== $newInt;
            error_log("DEBUG: {$field} (int comparison) changed: " . ($changed ? 'YES' : 'NO'));
            return $changed;
        }

        // Pour les champs booléens, comparer en tant que booléens
        $booleanFields = [
            'register.customer_management',
            'register.arrondissement_method',
            'generator.barcode',
            'email.active',
            'referent_lot_optionnel',
            'date_expiration_optionnel',
            'items.sous_type',
            'suppliers_enabled'
        ];

        if (in_array($field, $booleanFields)) {
            $oldBool = (bool)$oldValue;
            $newBool = (bool)$newValue;
            $changed = $oldBool !== $newBool;
            error_log("DEBUG: {$field} (bool comparison) changed: " . ($changed ? 'YES' : 'NO'));
            return $changed;
        }

        // Pour les autres champs, comparaison stricte
        $hasChanged = $oldValue !== $newValue;

        error_log("DEBUG: {$field} (strict comparison) changed: " . ($hasChanged ? 'YES' : 'NO'));

        return $hasChanged;
    }

    /**
     * Normalise une valeur pour la comparaison
     */
    private function normalizeValue($value)
    {
        // Convertir les chaînes vides en null
        if ($value === '') {
            return null;
        }

        // Convertir les chaînes "null" en null
        if ($value === 'null') {
            return null;
        }

        // Convertir les chaînes "undefined" en null
        if ($value === 'undefined') {
            return null;
        }

        // Pour les nombres, normaliser en string pour éviter les problèmes de type
        if (is_numeric($value)) {
            return (string)$value;
        }

        // Pour les booléens, normaliser
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        // Pour les chaînes, supprimer les espaces en début et fin
        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }

    /**
     * Confirme et sauvegarde les modifications
     */
    public function confirmChanges()
    {
        Log::info('DEBUG: Méthode confirmChanges appelée');

        $changes = session('settings_changes', []);
        Log::info('DEBUG: Modifications en session:', $changes);

        if (empty($changes)) {
            Log::info('DEBUG: Aucune modification à confirmer');
            return response()->json([
                'success' => false,
                'message' => 'Aucune modification à confirmer.'
            ]);
        }

        $success = true;
        $errors = [];

        // Sauvegarder chaque modification
        foreach ($changes as $field => $change) {
            try {
                Log::info("DEBUG: Tentative de sauvegarde du champ '{$field}' avec la valeur: " . json_encode($change['new']));

                if (!SettingsService::set($field, $change['new'])) {
                    $success = false;
                    $fieldLabel = $this->getFieldLabel($field);
                    $errors[] = "Impossible de sauvegarder le paramètre '{$fieldLabel}'. Vérifiez les permissions du fichier de configuration.";
                    Log::error("DEBUG: Échec de la sauvegarde pour le champ '{$field}'");
                } else {
                    Log::info("DEBUG: Sauvegarde réussie pour le champ '{$field}'");
                }
            } catch (Exception $e) {
                $success = false;
                $fieldLabel = $this->getFieldLabel($field);
                $errors[] = "Erreur lors de la sauvegarde de '{$fieldLabel}': " . $e->getMessage();
                Log::error("DEBUG: Exception lors de la sauvegarde du champ '{$field}': " . $e->getMessage());
            }
        }

        // Nettoyer la session
        session()->forget('settings_changes');

        if ($success) {
            // Récupérer les paramètres mis à jour pour les retourner au frontend
            $updatedSettings = $this->formatSettingsForView(config('custom'));

            return response()->json([
                'success' => true,
                'message' => 'Modifications confirmées et sauvegardées avec succès.',
                'updatedSettings' => $updatedSettings
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde des modifications.',
                'errors' => $errors
            ]);
        }
    }

    /**
     * Retourne le label d'un champ pour l'affichage
     */
    public function getFieldLabel($field)
    {
        $labels = [
            'register.tva_default' => 'TVA par défaut',
            'register.customer_management' => 'Gestion des clients',
            'register.arrondissement_method' => 'Méthode d\'arrondissement',
            'article.seuil' => 'Seuil d\'alerte stock',
            'generator.barcode' => 'Générateur de codes-barres',
            'email.active' => 'Notifications email',
            'barcode.prefix_one' => 'Préfixe 1',
            'barcode.prefix_two' => 'Préfixe 2',
            'referent_lot_optionnel' => 'Référent lot optionnel',
            'date_expiration_optionnel' => 'Date d\'expiration optionnelle',
            'loyalty_point_step' => 'Pas des points de fidélité',
            'items.sous_type' => 'Sous-types',
            'suppliers_enabled' => 'Fournisseurs',
            'company.address_street' => 'Adresse',
            'company.address_postal' => 'Code postal',
            'company.address_city' => 'Ville',
            'company.address_country' => 'Pays',
            'company.tva_number' => 'Numéro de TVA',
            'company.phone' => 'Téléphone',
        ];

        return $labels[$field] ?? $field;
    }

    /**
     * Annule une modification spécifique
     */
    public function cancelChange(Request $request)
    {
        $field = $request->input('field');
        $changes = session('settings_changes', []);

        if (isset($changes[$field])) {
            // Restaurer l'ancienne valeur
            SettingsService::set($field, $changes[$field]['old']);

            // Retirer de la liste des changements
            unset($changes[$field]);
            session(['settings_changes' => $changes]);

            // Récupérer les paramètres mis à jour
            $updatedSettings = $this->formatSettingsForView(config('custom'));

            return response()->json([
                'success' => true,
                'message' => 'Modification annulée.',
                'remaining_changes' => count($changes),
                'updatedSettings' => $updatedSettings
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Modification non trouvée.'], 404);
    }

    /**
     * Récupère les modifications depuis la session
     */
    public function getChanges()
    {
        $changes = session('settings_changes', []);

        return response()->json([
            'success' => true,
            'changes' => $changes
        ]);
    }

    /**
     * Réinitialise les paramètres aux valeurs par défaut
     */
    public function reset()
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        try {
            if (SettingsService::reset()) {
                return redirect()->route('settings.system.index')
                    ->with('success', 'Paramètres réinitialisés aux valeurs par défaut.');
            } else {
                return redirect()->route('settings.system.index')
                    ->withErrors(['error' => 'Erreur lors de la réinitialisation.']);
            }
        } catch (Exception $e) {
            return redirect()->route('settings.system.index')
                ->withErrors(['error' => 'Erreur lors de la réinitialisation : ' . $e->getMessage()]);
        }
    }

    /**
     * Remplit les coordonnées de l'entreprise avec les valeurs du .env
     */
    public function fillFromEnv()
    {
        $permissionCheck = $this->checkAdminPermissions();
        if ($permissionCheck) return $permissionCheck;

        try {
            $updated = false;

            // Mapping des champs de coordonnées avec les variables d'environnement
            $envMapping = [
                'company.address_street' => 'CUSTOM_ADDRESS_STREET',
                'company.address_postal' => 'CUSTOM_ADDRESS_POSTAL',
                'company.address_city' => 'CUSTOM_ADDRESS_CITY',
                'company.address_country' => 'CUSTOM_ADDRESS_COUNTRY',
                'company.tva_number' => 'CUSTOM_TVA_NUMBER',
                'company.phone' => 'CUSTOM_PHONE',
            ];

            foreach ($envMapping as $settingKey => $envKey) {
                $envValue = env($envKey);
                if (!empty($envValue)) {
                    if (SettingsService::set($settingKey, (string)$envValue)) {
                        $updated = true;
                    }
                }
            }

            if ($updated) {
                // Récupérer les paramètres mis à jour
                $updatedSettings = $this->formatSettingsForView(config('custom'));

                return response()->json([
                    'success' => true,
                    'message' => 'Coordonnées remplies avec succès depuis le fichier .env',
                    'updatedSettings' => $updatedSettings
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune valeur trouvée dans le fichier .env ou aucune mise à jour effectuée'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du remplissage : ' . $e->getMessage()
            ]);
        }
    }
}
