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

---

## Installation et configuration

### 1. Vérifier les installations 
```
composer require symfony/http-client
```
```
composer require --dev symfony/maker-bundle
```
### 2. Lancer le projet
```
symfony serve
```
