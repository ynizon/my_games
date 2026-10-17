# MyGames — application Android

Version Android native (Kotlin, Jetpack Compose) des jeux du site : Les Trois Manches, Checklist, Mots Interdits, Croquis Express et Les Loups du Village.
Elle utilise l'API Laravel décrite dans [`../docs/api.md`](../docs/api.md).

## Fonctionnement

- Au lancement, l'application compare la version des cartes (`cards/version`) et ne retélécharge le catalogue que s'il a changé. Il est gardé sur le téléphone : **on peut jouer sans connexion**.
- Une copie des cartes est intégrée à l'application (`app/src/main/assets/catalog_fr.json`) : elle est jouable **dès le premier lancement, même sans connexion**, puis mise à jour avec le site au premier démarrage connecté. Avant de compiler une nouvelle version, régénérer cette copie depuis le site : `php artisan catalog:export`.
- Les cartes sont dédoublonnées à chaque chargement : même id, ou même nom dans un même jeu (sans tenir compte des accents, de la casse ni de la ponctuation).
- Les règles des jeux sont celles du site (temps, manches, points, corrections en fin de tour, cartes déjà jouées aujourd'hui évitées pour Les Trois Manches et Checklist).
- Loup-Garou : la distribution se fait sur le téléphone (règles fournies par l'API : `role`, `copies`, `is_wolf`), puis le téléphone passe de joueur en joueur. Les images des rôles sont embarquées.
- Compte (facultatif) : connexion / inscription par jeton Sanctum, profil, et « Mes cartes » pour proposer, modifier ou supprimer ses cartes (validation par un administrateur ; un compte `card-edit` peut valider directement).

## Structure

| Dossier | Contenu |
|---|---|
| `data/` | Modèles JSON, client Retrofit, session (jeton), catalogue hors connexion |
| `game/` | Logique pure des jeux, testée dans `src/test` |
| `ui/` | Écrans Compose, thème, navigation ; `ui/games` : parties, `ui/account` : compte et cartes |

## Compiler

Prérequis : Android Studio (JDK 21 fourni) ou `JAVA_HOME` vers un JDK 17+, SDK Android 36 (`local.properties` : `sdk.dir=...`).

```bash
./gradlew testDebugUnitTest   # tests de la logique des jeux
./gradlew assembleDebug       # app/build/outputs/apk/debug/app-debug.apk
```

- **Debug** : l'API est `http://10.0.2.2:8000/api/v1/` (le PC vu depuis l'émulateur) ; lancer le site avec `php artisan serve --host=0.0.0.0 --port=8000`. Autre adresse : `./gradlew assembleDebug -PapiBaseUrl=http://192.168.1.10:8000/api/v1/` (penser à l'ajouter dans `res/xml/network_security_config.xml` si c'est du HTTP).
- **Release** : `https://games.gameandme.fr/api/v1/`. Il faut signer l'APK/AAB avec votre clé (Android Studio > Build > Generate Signed Bundle).
