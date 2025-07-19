# Configuration TVA par défaut

## Nouvelle fonctionnalité

Une nouvelle configuration a été ajoutée pour définir une TVA par défaut lors de la création d'articles.

## Configuration

### Variable d'environnement

Ajoutez cette ligne dans votre fichier `.env` :

```env
# TVA par défaut pour la création d'articles (ex: "21" pour 21%, laisser vide pour aucun défaut)
REGISTER_TVA_DEFAULT=""
```

### Exemples d'utilisation

- `REGISTER_TVA_DEFAULT="21"` : Le select TVA sera pré-sélectionné sur 21%
- `REGISTER_TVA_DEFAULT=""` : Aucune valeur par défaut, le select affiche "Sélectionner la TVA"
- `REGISTER_TVA_DEFAULT="6"` : Le select TVA sera pré-sélectionné sur 6%

## Fonctionnement

1. **Création d'un nouvel article** : Si `REGISTER_TVA_DEFAULT` est défini, le select TVA sera automatiquement
   pré-sélectionné avec cette valeur
2. **Modification d'un article existant** : La valeur existante de l'article prend la priorité sur la valeur par défaut
3. **Validation** : Les taux de TVA acceptés sont : 0%, 6%, 12%, 21%

## Fichiers modifiés

- `config/custom.php` : Ajout de la configuration `register.tva_default`
- `app/Http/Controllers/Inventory/CreationController.php` : Passage de la valeur par défaut à la vue
- `resources/views/panel/inventory/create/step-one.blade.php` : Utilisation de la valeur par défaut dans le select 
