# Recettes AI - Assistant Ingrédient

## Présentation

Recettes AI (Assistant Ingrédient) est une application web (Web Progressive App) permettant aux utilisateurs de trouver des recettes de cuisine en fonction des ingrédients qu'ils possèdent déjà chez eux. L'application offre également des fonctionnalités de gestion de recettes favorites et de filtrage par catégories ou étiquettes.

Cette application est un MVP (Model Viable Product).

## Fonctionnalités

### Utilisateurs
- Recherche de recettes par ingrédients disponibles
- Création d'une liste personnelle d'ingrédients pour l'utilisateur
- Sauvegarde de recettes en favoris de l'utilisateur
- Filtrage des recettes par difficulté, temps de préparation, catégories, etc.
- Gestion du compte utilisateur (paramètres, mot de passe)
- Gestion du compte utilisateur (Favoris & Ingredients)

### Administration
- Gestion complète des recettes (ajout, modification, suppression)
- Gestion des catégories et étiquettes
- Gestion des ingrédients et unités de mesure
- Gestion des utilisateurs et administrateurs
- Tableau de bord administratif

## Technologies utilisées

- PHP 8 (POO, MVC)
- MySQL
- HTML5
- CSS3
- JavaScript
- Architecture MVC personnalisée

## Installation

### Prérequis
- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx, etc.)

### Étapes d'installation

1. Cloner le dépôt
```bash
git clone [url_du_repo] recettesaipoo
```

2. Configurer le fichier de connexion à la base de données (à créer dans app/config/)

3. Importer la base de données à partir du fichier SQL fourni

4. Configurer la constante RACINE_SITE dans public/index.php selon votre environnement
```php
define("RACINE_SITE", "http://localhost/recettesaipoo/");
```

5. Accéder à l'application via l'URL configurée

## Structure du projet

```
recettesaipoo/
├── app/
│   ├── controllers/      # Contrôleurs de l'application
│   ├── core/            # Classes de base du framework
│   ├── helpers/         # Fonctions utilitaires
│   ├── models/          # Modèles pour l'accès aux données
│   └── views/           # Templates et vues
├── public/
│   ├── assets/          # Ressources statiques (CSS, JS, images)
│   │   ├── css/
│   │   ├── javascript/
│   │   ├── img/
│   │   └── recettes/    # Images des recettes
│   └── index.php        # Point d'entrée de l'application
└── README.md
```

## Interfaces de l'application

### Interface publique
- Page d'accueil
- Catalogue de recettes avec filtres
- Détails de recette
- Inscription et connexion
- Gestion du profil utilisateur

### Interface d'administration
- Tableau de bord
- Gestion des recettes
- Gestion des ingrédients et unités
- Gestion des catégories et étiquettes
- Gestion des utilisateurs

## Auteur

Développé par [Alexandre Graziani](https://agwebcreation.fr)

## Licence

Tous droits réservés