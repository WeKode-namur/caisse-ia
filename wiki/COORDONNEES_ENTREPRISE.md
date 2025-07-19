# Configuration des coordonnées de l'entreprise

## Variables d'environnement

Pour activer l'auto-remplissage des coordonnées de l'entreprise depuis le fichier `.env`, ajoutez les variables
suivantes à votre fichier `.env` :

```env
# Coordonnées de l'entreprise (optionnel - pour auto-remplissage des paramètres)
CUSTOM_ADDRESS_STREET="1, Rue du Bureau"
CUSTOM_ADDRESS_POSTAL="1234"
CUSTOM_ADDRESS_CITY="Ville"
CUSTOM_ADDRESS_COUNTRY="Belgique"
CUSTOM_TVA_NUMBER="BE 0844.111.222"
CUSTOM_PHONE="081 22 33 44"
```

## Fonctionnalités

### 1. Auto-remplissage automatique

- Les coordonnées sont automatiquement pré-remplies avec les valeurs du `.env` si elles sont vides dans les paramètres
- Cela se fait à chaque chargement de la page des paramètres système

### 2. Bouton "Remplir depuis .env"

- Un bouton est disponible dans l'onglet "Coordonnées entreprise"
- Permet de remplacer toutes les coordonnées actuelles par les valeurs du `.env`
- Demande une confirmation avant de procéder

### 3. Variables supportées

- `CUSTOM_ADDRESS_STREET` : Adresse de l'entreprise
- `CUSTOM_ADDRESS_POSTAL` : Code postal
- `CUSTOM_ADDRESS_CITY` : Ville
- `CUSTOM_ADDRESS_COUNTRY` : Pays
- `CUSTOM_TVA_NUMBER` : Numéro de TVA
- `CUSTOM_PHONE` : Numéro de téléphone

## Utilisation

1. Ajoutez les variables dans votre fichier `.env`
2. Accédez aux paramètres système
3. Allez dans l'onglet "Coordonnées entreprise"
4. Les champs vides seront automatiquement remplis avec les valeurs du `.env`
5. Ou utilisez le bouton "Remplir depuis .env" pour remplacer toutes les valeurs

## Notes

- Les variables sont optionnelles
- Si une variable n'est pas définie ou est vide, elle sera ignorée
- Les valeurs existantes dans les paramètres ne seront pas écrasées automatiquement
- Seules les valeurs vides sont remplies automatiquement 
