# Système de Paramètres Système

## Vue d'ensemble

Le système de paramètres système permet de configurer dynamiquement l'application selon le niveau d'accès de
l'utilisateur. Les paramètres sont stockés dans un fichier JSON (`public/data/settings.json`) et peuvent être modifiés
via l'interface web sans redémarrage.

## Niveaux d'accès

### Niveau 80 (Manager)

- **TVA par défaut** : Définir la TVA pré-sélectionnée lors de la création d'articles
- **Pas des points de fidélité** : Configurer le pas pour l'attribution des points de fidélité

### Niveau 100 (Administrateur)

Tous les paramètres du niveau 80, plus :

#### Paramètres de base

- TVA par défaut
- Pas des points de fidélité

#### Paramètres avancés

- **Configuration de la caisse** :
    - Gestion des clients
    - Méthode d'arrondissement
- **Configuration des articles** :
    - Seuil d'alerte stock
    - Sous-types
    - Fournisseurs
- **Configuration des codes-barres** :
    - Générateur automatique
    - Préfixe 1 et 2
- **Options générales** :
    - Notifications email
    - Référent lot optionnel
    - Date d'expiration optionnelle

#### Coordonnées entreprise

- Adresse
- Code postal
- Ville
- Pays
- Numéro de TVA
- Téléphone

## Fonctionnalités

### Interface avec onglets

- **Onglets dynamiques** : L'interface s'adapte selon le niveau d'utilisateur
- **Navigation intuitive** : Onglets organisés par catégories
- **Design responsive** : Interface adaptée mobile et desktop

### Modal de confirmation

- **Historique des modifications** : Affichage des changements effectués
- **Tableau comparatif** : Ancienne vs nouvelle valeur
- **Actions par ligne** : Possibilité d'annuler une modification spécifique
- **Boutons d'action** :
    - Continuer les modifications
    - Confirmer toutes les modifications

### Système de permissions

- **Validation dynamique** : Règles de validation selon le niveau
- **Champs autorisés** : Seuls les champs autorisés sont traités
- **Sécurité** : Vérification des permissions à chaque action

## Structure technique

### Fichiers principaux

- `app/Services/SettingsService.php` : Service de gestion des paramètres
- `app/Http/Controllers/Settings/SystemSettingsController.php` : Contrôleur avec permissions
- `resources/views/panel/settings/system/index.blade.php` : Interface utilisateur
- `public/data/settings.json` : Stockage des paramètres
- `config/custom.php` : Configuration avec fallback

### Routes

```php
// Paramètres système
Route::prefix('system')->name('system.')->group(function () {
    Route::get('/', [SystemSettingsController::class, 'index'])->name('index');
    Route::post('/', [SystemSettingsController::class, 'update'])->name('update');
    Route::post('/confirm', [SystemSettingsController::class, 'confirmChanges'])->name('confirm');
    Route::post('/cancel-change', [SystemSettingsController::class, 'cancelChange'])->name('cancel-change');
    Route::get('/reset', [SystemSettingsController::class, 'reset'])->name('reset');
});
```

### Méthodes du contrôleur

- `index()` : Affichage de l'interface
- `update()` : Mise à jour des paramètres avec validation
- `confirmChanges()` : Confirmation des modifications
- `cancelChange()` : Annulation d'une modification spécifique
- `reset()` : Réinitialisation aux valeurs par défaut

## Utilisation

### Accès à l'interface

1. Aller dans **Paramètres** > **Paramètres système**
2. L'interface s'adapte automatiquement selon le niveau d'utilisateur

### Modification des paramètres

1. Naviguer entre les onglets selon les permissions
2. Modifier les valeurs souhaitées
3. Cliquer sur **Sauvegarder**
4. Le modal de confirmation s'affiche avec les modifications
5. Choisir de confirmer ou continuer les modifications

### Annulation de modifications

- **Par ligne** : Cliquer sur "Annuler" dans le modal
- **Toutes** : Fermer le modal et continuer les modifications

## Fallback et compatibilité

Le système utilise un mécanisme de fallback :

1. **Fichier JSON** : Valeur principale
2. **Variables d'environnement** : Fallback si pas dans JSON
3. **Valeurs par défaut** : Fallback final

Exemple dans `config/custom.php` :

```php
'tva' => SettingsService::get('company.tva_number', env('CUSTOM_TVA_NUMBER')),
```

## Sécurité

- **Validation des permissions** : Vérification du niveau d'administrateur
- **Validation des données** : Règles de validation selon le niveau
- **Protection CSRF** : Tokens pour toutes les actions
- **Sanitisation** : Nettoyage des données d'entrée

## Maintenance

### Ajout de nouveaux paramètres

1. Ajouter dans `SettingsService::getDefaultSettings()`
2. Ajouter dans `getValidationRules()` et `getAllowedFields()`
3. Ajouter dans l'interface utilisateur
4. Mettre à jour la documentation

### Migration depuis .env

Les paramètres existants dans `.env` continuent de fonctionner comme fallback, permettant une migration en douceur. 
