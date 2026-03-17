# TV-TIME 🎬

TV-TIME est une application web moderne pour suivre vos films et séries préférés, créer des listes de visionnage et explorer les dernières sorties. Initialement conçu avec Symfony, le projet a été entièrement migré vers une architecture **Fullstack Nuxt 3/4**.

## 🚀 Technologies

- **Frontend & Backend** : [Nuxt 3/4](https://nuxt.com/) (Vue.js + Nitro)
- **Base de Données** : MySQL
- **ORM** : [Prisma](https://www.prisma.io/)
- **Design** : Tailwind CSS + Lucide Icons
- **API** : Integration complète avec [TMDB](https://www.themoviedb.org/)

## 🛠️ Installation

### 1. Prérequis
- Node.js (v18+)
- MySQL

### 2. Cloner et installer
```bash
# Installer les dépendances
npm install
```

### 3. Configuration
Créez un fichier `.env` à la racine :

```env
DATABASE_URL="mysql://USER:PASSWORD@HOST:PORT/DATABASE"
TMDB_TOKEN="VOTRE_TOKEN_API_TMDB"
```

### 4. Base de données
Initialisez votre base de données avec Prisma :

```bash
npx prisma db push
```

## 💻 Utilisation

Pour lancer le serveur de développement :

```bash
npm run dev
```

L'application sera accessible sur [http://localhost:3000](http://localhost:3000).

## ✨ Fonctionnalités Clés

- **Synchronisation TMDB** : Cliquez sur le bouton 🔄 dans la navigation pour importer les derniers films et séries.
- **Authentification** : Créez un compte et connectez-vous pour accéder à vos listes.
- **Détails & Recherche** : Explorez les fiches détaillées et recherchez du contenu en temps réel.
- **Gestion de Liste** : Ajoutez du contenu à votre watchlist personnelle (nécessite connexion).

## 📁 Structure du Projet

- `app/` : Pages Vue, composants et layouts (Nuxt 4 structure).
- `server/api/` : Endpoints API (Node.js/TypeScript).
- `prisma/` : Schéma de la base de données.

## 💎 Comprendre Prisma

Prisma est l'**ORM** (Object-Relational Mapper) qui remplace Doctrine dans ce projet. Il permet de manipuler la base de données en utilisant du code TypeScript au lieu de SQL brut.

### Concepts Clés :
- **Le Schéma (`prisma/schema.prisma`)** : C'est le fichier central où sont définies vos tables (modèles). C'est l'équivalent des annotations/attributs dans vos anciennes entités Symfony.
- **Prisma Client** : C'est l'outil qui génère automatiquement les méthodes pour intéragir avec vos données (ex: `prisma.movie.findMany()`).

### Commandes Utiles :
- **`npx prisma db push`** : Synchronise votre schéma (`.prisma`) avec votre base de données locale. Utile après avoir modifié une table.
- **`npx prisma studio`** : Ouvre une interface web (généralement sur `http://localhost:5555`) qui vous permet de voir, modifier et supprimer vos données très facilement, comme dans un tableur.
- **`npx prisma generate`** : Regénère le client TypeScript. À faire si vous constatez que l'autocomplétion ne reconnaît pas un nouveau champ.

---

