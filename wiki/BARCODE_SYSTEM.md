# Système de Gestion des Codes-Barres

## Vue d'ensemble

Le système de gestion des codes-barres permet de gérer les codes-barres des variants de deux manières différentes selon la configuration :

1. **Générateur automatique activé** : Les codes-barres sont générés automatiquement et ne sont jamais visibles ni éditables à la création du variant.
2. **Générateur automatique désactivé** : Les codes-barres doivent être saisis manuellement par l'utilisateur, le champ est alors visible et obligatoire.

## Configuration

### Variable d'environnement

```env
ENABLE_BARCODE_GENERATOR=false
```

- `true` : Générateur automatique activé
- `false` : Générateur automatique désactivé (codes-barres manuels obligatoires)

### Configuration dans `config/custom.php`

```php
'generator' => [
    'barcode' => env('ENABLE_BARCODE_GENERATOR', false),
],
```

## Fonctionnalités

### 1. Générateur Automatique Activé (`ENABLE_BARCODE_GENERATOR=true`)

**Interface utilisateur :**
- Aucun champ de saisie pour le code-barres n'est affiché dans le formulaire de création/édition de variant.
- L'utilisateur ne voit jamais le code-barres à la création.

**Comportement :**
- Le code-barres est généré automatiquement lors de la sauvegarde du variant côté serveur.
- L'utilisateur ne peut ni voir ni éditer le code-barres à la création.
- Format : `PREFIX1PREFIX2YYMMDDXXXX` (ex: WKNAM2412010001)

**Validation :**
- Code-barres non éditable, non visible.
- Unicité garantie par la génération automatique.

### 2. Générateur Automatique Désactivé (`ENABLE_BARCODE_GENERATOR=false`)

**Interface utilisateur :**
- Champ de saisie obligatoire pour le code-barres affiché dans le formulaire.
- Indicateur visuel (*) pour indiquer que le champ est obligatoire.
- Message d'aide : "Code-barres obligatoire - Générateur automatique désactivé"

**Comportement :**
- Le code-barres est obligatoire lors de la création d'un variant.
- Aucun code-barres n'est généré automatiquement.
- L'utilisateur doit saisir manuellement chaque code-barres.

**Validation :**
- Code-barres obligatoire
- Vérification d'unicité
- Message d'erreur personnalisé si manquant

## Implémentation Technique

### Frontend (JavaScript/Alpine.js)

**Affichage conditionnel :**
```html
<template x-if="!barcodeGeneratorEnabled">
    <!-- Champ code-barres ici -->
</template>
```

**Aucun champ code-barres n'est rendu si le générateur est activé.**

### Backend (PHP/Laravel)

**Validation conditionnelle :**
```php
$barcodeGeneratorEnabled = config('custom.generator.barcode', false);
$barcodeRules = $barcodeGeneratorEnabled 
    ? 'nullable|string|unique:variants,barcode,' . ($request->variant_id ?? 'null')
    : 'required|string|unique:variants,barcode,' . ($request->variant_id ?? 'null');
```

**Génération automatique :**
```php
if ($barcodeGeneratorEnabled && empty($barcode)) {
    $barcode = VariantService::generateCustomBarcode();
}
```

**Finalisation d'article :**
```php
if (empty($variant->barcode)) {
    if ($barcodeGeneratorEnabled) {
        $variant->barcode = VariantService::generateCustomBarcode();
        $variant->save();
    } else {
        throw new \Exception("Le variant #{$variant->id} n'a pas de code-barres et le générateur automatique est désactivé.");
    }
}
```

## UX Résumée

- **Générateur activé** : L'utilisateur ne voit jamais le champ code-barres, tout est automatique.
- **Générateur désactivé** : L'utilisateur doit obligatoirement saisir un code-barres, le champ est visible et contrôlé.

## Migration et compatibilité

Le système est rétrocompatible :
- Les variants existants sans code-barres continuent de fonctionner si le générateur est activé
- Les variants avec code-barres existants ne sont pas affectés
- La configuration peut être modifiée à tout moment

