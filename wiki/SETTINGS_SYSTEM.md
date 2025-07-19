# Système de Paramètres JSON

## Vue d'ensemble

Le système de paramètres JSON permet de gérer la configuration de l'application de manière dynamique sans avoir besoin
de redémarrer le serveur ou de vider les caches. Les paramètres sont stockés dans `public/data/settings.json` et peuvent
être modifiés via l'interface d'administration.

## Avantages par rapport au système .env

1. **Modification dynamique** : Pas besoin de redémarrer le serveur
2. **Interface utilisateur** : Modification via l'interface web
3. **Pas de cache** : Les modifications sont immédiatement effectives
4. **Sauvegarde automatique** : Les paramètres sont sauvegardés automatiquement
5. **Fallback vers .env** : Si le fichier JSON n'existe pas, utilisation des variables .env

## Structure du fichier settings.json

```json
{
    "register": {
        "tva_default": null,
        "customer_management": false,
        "arrondissement_method": false
    },
    "article": {
        "seuil": 5
    },
    "generator": {
        "barcode": false
    },
    "email": {
        "active": false
    },
    "barcode": {
        "prefix_one": "WK",
        "prefix_two": "NAM"
    },
    "referent_lot_optionnel": true,
    "date_expiration_optionnel": true,
    "loyalty_point_step": 1,
    "items": {
        "sous_type": false
    },
    "suppliers_enabled": false
}
```

## Utilisation dans le code

### Dans les contrôleurs

```php
use App\Services\SettingsService;

// Récupérer un paramètre
$tvaDefault = SettingsService::get('register.tva_default');

// Récupérer avec valeur par défaut
$seuil = SettingsService::get('article.seuil', 5);

// Mettre à jour un paramètre
SettingsService::set('register.tva_default', 21);
```

### Dans les vues Blade

```php
{{-- Utilisation directe --}}
@if($settings->get('register.customer_management'))
    <!-- Affichage de la gestion des clients -->
@endif

{{-- Utilisation avec helper --}}
@if($settings->isCustomerManagementEnabled())
    <!-- Affichage de la gestion des clients -->
@endif

{{-- TVA par défaut --}}
{{ $settings->getDefaultTva() }}
```

### Dans la configuration (config/custom.php)

```php
use App\Services\SettingsService;

return [
    'register' => [
        'tva_default' => SettingsService::get('register.tva_default', env('REGISTER_TVA_DEFAULT', null)),
        'customer_management' => SettingsService::get('register.customer_management', env('REGISTER_CUSTOMER_MANAGEMENT', false)),
    ],
    // ...
];
```

## Interface d'administration

### Accès

- Aller dans **Paramètres** > **Paramètres système**
- Niveau d'administrateur requis : 80+

### Fonctionnalités

1. **Modification des paramètres** : Interface intuitive avec validation
2. **Réinitialisation** : Retour aux valeurs par défaut
3. **Validation** : Vérification des types et valeurs
4. **Sauvegarde automatique** : Pas de perte de données

### Paramètres configurables

#### Configuration de la caisse

- **TVA par défaut** : 0%, 6%, 12%, 21% ou aucune valeur par défaut
- **Gestion des clients** : Activer/désactiver la gestion des clients
- **Arrondissement** : Activer/désactiver l'arrondissement des prix

#### Configuration des articles

- **Seuil d'alerte stock** : Nombre minimum pour les alertes
- **Sous-types** : Activer/désactiver les sous-types
- **Fournisseurs** : Activer/désactiver la gestion des fournisseurs

#### Configuration des codes-barres

- **Générateur automatique** : Activer/désactiver la génération automatique
- **Préfixe 1** : Premier préfixe pour les codes-barres
- **Préfixe 2** : Deuxième préfixe pour les codes-barres

#### Options générales

- **Notifications email** : Activer/désactiver les emails
- **Référent lot optionnel** : Rendre le référent de lot optionnel
- **Date d'expiration optionnelle** : Rendre la date d'expiration optionnelle
- **Pas des points de fidélité** : Valeur pour l'attribution des points

## Migration depuis le système .env

### Variables .env remplacées

- `REGISTER_TVA_DEFAULT` → `settings.json: register.tva_default`
- `REGISTER_CUSTOMER_MANAGEMENT` → `settings.json: register.customer_management`
- `REGISTER_ARRONDISSEMENT_METHOD` → `settings.json: register.arrondissement_method`
- `ARTICLE_SEUIL` → `settings.json: article.seuil`
- `ENABLE_BARCODE_GENERATOR` → `settings.json: generator.barcode`
- `MAIL_ACTIVE` → `settings.json: email.active`
- `PREFIX_ONE` → `settings.json: barcode.prefix_one`
- `PREFIX_TWO` → `settings.json: barcode.prefix_two`
- `REFERENT_LOT_OPTIONNEL` → `settings.json: referent_lot_optionnel`
- `DATE_EXPIRATION_OPTIONNEL` → `settings.json: date_expiration_optionnel`
- `LOYALTY_POINT_STEP` → `settings.json: loyalty_point_step`
- `CUSTOM_ITEMS_SOUS_TYPE` → `settings.json: items.sous_type`
- `SUPPLIERS_ENABLED` → `settings.json: suppliers_enabled`

### Compatibilité

Le système maintient la compatibilité avec les variables .env en tant que fallback :

1. Lecture du fichier `settings.json`
2. Si le paramètre n'existe pas, utilisation de la variable .env
3. Si la variable .env n'existe pas, utilisation de la valeur par défaut

## Sécurité

- **Permissions** : Accès réservé aux administrateurs (niveau 80+)
- **Validation** : Toutes les entrées sont validées
- **Logs** : Les erreurs sont loggées
- **Fallback** : En cas d'erreur, utilisation des valeurs par défaut

## Maintenance

### Sauvegarde

Le fichier `public/data/settings.json` doit être inclus dans les sauvegardes.

### Migration

Pour migrer vers un nouveau serveur :

1. Copier le fichier `public/data/settings.json`
2. Les paramètres seront automatiquement chargés

### Réinitialisation

En cas de problème, le fichier peut être supprimé et sera recréé avec les valeurs par défaut. 
