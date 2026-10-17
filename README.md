## A propos de l'application

Cette application est basée sur Laravel. Elle répond en PWA et peut donc être installé et jouée sur mobile en mode deconnecté.
Le but était de permettre d'avoir un système de gestion de cartes, afin de pouvoir fournir diférents types de jeux.
Plusieurs jeux ont étés ajoutés avec plus ou moins de cartes. Certains d'entre eux ne sont pas encore finalisées.

- Les Trois Manches (personnages célèbres en 3 manches)
- Mots Interdits (faire deviner sans dire les mots interdits)
- Croquis Express (faire deviner en dessinant)
- Checklist (trouver un maximum de réponses sur un thème)
- Petits meurtres et faits divers
- Les Loups du Village (villageois contre loups-garous)

Les noms et les illustrations sont propres au site : ces jeux s'inspirent de jeux de société existants,
sans être affiliés à leurs éditeurs. Illustrations des rôles : emojis Noto © Google (SIL OFL 1.1).

Pour tester cette application, rendez vous sur
https://games.gameandme.fr

Chaque membre peut ajouter des cartes, cela permet de lever les limites des jeux de cartes papiers.


## Installation

Creer un site avec le repertoire public comme root.
Définissez vos constantes dans le répertoire config/app.php et config/database.php
Creer une base de données mysql ("mygames" par défaut),
puis faites les commandes:

```html
composer update
php artisan migrate
```

## Connexion

- admin@admin.com / admin
- manager@manager.com / manager
