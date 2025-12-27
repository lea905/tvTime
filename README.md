# TvTime

**TvTime** est une application web qui permet de répertorier et d'explorer des **films** et **séries TV**.  
Les utilisateurs peuvent créer leurs propres **listes personnalisées** (avec un nom et une description), marquer les œuvres qu’ils ont **visionnées**, et indiquer les **émotions** ressenties pendant le visionnage.

---

## Fonctionnalités principales

- Affichage des films et séries issus de l’API TMDb.
- Création de listes personnalisées par l’utilisateur.
- Indication d’un visionnage avec les émotions associées.
- Gestion des utilisateurs (connexion, enregistrement, etc.).
- Persistance des données en **base de données MySQL** via **Doctrine ORM**.

---

## Technologies et outils utilisés

| Catégorie | Outils |
|------------|--------|
| **Backend** | Symfony (PHP) |
| **Frontend** | Twig, Bootstrap, Webpack Encore |
| **Base de données** | MySQL (avec Doctrine) |
| **API externe** | The Movie Database (TMDb) |

---

## Architecture et éléments mis en place

### 1. Liaison API
- Intégration avec l’API TMDb pour récupérer les films, séries et genres.
- Utilisation d’un service dédié pour gérer les requêtes HTTP et le cache des résultats.

### 2. Enregistrement en base de données
- Sauvegarde des films, séries et listes créées par l’utilisateur.
- Gestion des relations entre **utilisateur ↔ liste ↔ œuvre** avec Doctrine.

### 3. Interface utilisateur
- Utilisation de **Bootstrap** pour le design et la responsivité.
- Intégration via **Webpack Encore** pour la compilation des assets (CSS/JS).

### 4. Validation HTML et sémantique
- Utilisation des balises sémantiques : ```<main>, <section>, <article>, <header>```.
- Code HTML conforme aux bonnes pratiques W3C.

### 5. Micro-data (Schema.org)
- Implémentation des micro-données pour les images, les dates et les titres.

### 6. Esthétique et Bootstrap
- Redéfinition des variables de couleurs.
- Minification, réduction des imports inutiles.
- Thème cohérent sur l’ensemble du site.
- Harmonisation des couleurs et des espacements.
- Utilisation de Flexbox et des utilitaires Bootstrap.
- Scroll horizontal contrôlé pour l’affichage des listes sur mobile.

### 7. Fonctionnement interne
- Compilation et minification des assets via Webpack Encore.
- Images fournies par l’API TMDb via un CDN externe.
- Chargement optimisé sans stockage local inutile.

---

## Installation et configuration

### 1. Vérifier les installations 
```
composer require symfony/http-client
```
```
composer require --dev symfony/maker-bundle
```

### 2. Crée votre base de données
Copier le .env en .env.local

Modifier cette ligne en mettant vos informations
```
DATABASE_URL="mysql://p2303185:12303185@iutbg-lamp.univ-lyon1.fr:3306/p2303185?serverVersion=8.0.37"
```
Puis lancer cette commande
```
 php bin/console d:s:u -f
```

### 3. Lancer le projet
```
symfony serve
```
