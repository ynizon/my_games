# API mobile (v1)

Base : `https://<domaine>/api/v1` — réponses toujours en JSON.

Authentification par jeton Sanctum : `Authorization: Bearer <token>` (jeton renvoyé par `auth/register` et `auth/login`, sans expiration, révoqué par `auth/logout`). Un compte suspendu reçoit `403` et son jeton est révoqué.

Erreurs : `401` non connecté, `403` interdit, `404` introuvable, `422` validation (`{"message", "errors": {champ: [..]}}`), `429` trop de tentatives (6/min sur `auth/*`, 60/min ailleurs).

## Accès libre

| Méthode | URL | Paramètres | Réponse |
|---|---|---|---|
| GET | `config` | | `langs`, `default_lang`, `countries` |
| GET | `games` | | `data[]` : `id`, `slug`, `name`, `description`, `nb_words` (mots à saisir par carte), `settings` (bornes des réglages, ex. `nbteams: {min, max}`) |
| GET | `cards` | `lang`, `game_id` (optionnels) | `data[]` : cartes validées (`id`, `game_id`, `name`, `words[]`, `difficulty`…), `meta.version` |
| GET | `cards/version` | `lang` | `version` : si elle diffère de celle gardée par l'appli, retélécharger `cards` |
| GET | `werewolf/cards` | `lang` | Toutes les cartes du Loup-Garou avec `extension`, `image`, `help` et les règles de distribution : `role` (`villager`, `wolf`, `special` : seuls les spéciaux se choisissent), `copies`, `is_wolf` |
| POST | `werewolf/deck` | `nbplayers` (8-24), `nbwolfs` (2-4), `cards[]` (ids), `lang` | `deck[]` mélangé (une carte par joueur), `nbWolvesTotal`, `hasThief` ; `422` si trop de rôles |

Les cartes peuvent être téléchargées une fois puis le jeu fonctionne hors connexion (comme l'IndexedDB du site).

## Authentification

| Méthode | URL | Corps |
|---|---|---|
| POST | `auth/register` | `name`, `email`, `password` (min 5), `device_name` → `201 {token, token_type, user}` |
| POST | `auth/login` | `email`, `password`, `device_name` → `{token, token_type, user}` |
| POST | `auth/forgot-password` | `email` (envoie le lien de réinitialisation du site) |
| POST | `auth/logout` | 🔒 révoque le jeton courant → `204` |

## Connecté 🔒

| Méthode | URL | Corps / paramètres |
|---|---|---|
| GET | `me` | Utilisateur : `roles[]`, `permissions.card_edit`, `permissions.card_propose` |
| PUT | `me` | `name`, `email`, `nickname`, `password`, `lang` |
| GET | `my/cards` | `game_id` (optionnel). Toutes les cartes pour `card-edit`, sinon les siennes |
| GET | `my/cards/check-double` | `name`, `game_id`, `lang` : cartes au nom proche |
| POST | `my/cards` | `name`, `game_id`, `lang`, `country`, `difficulty` (1-3), `status`, `words[]` → `201` |
| GET / PUT / DELETE | `my/cards/{id}` | idem POST pour PUT |

Une carte créée ou modifiée par un utilisateur sans `card-edit` repasse en attente de validation (`status = 0`).
Nombre de mots par jeu : Checklist 10, Mots Interdits 5, Les Loups du Village 1 (extension), aucun pour Les Trois Manches et Croquis Express.
