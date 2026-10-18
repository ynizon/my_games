<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * Nouvelles histoires pour "Petits meurtres et faits divers" : une enquete, 6 suspects et 12 mots a placer.
 */
return new class extends Migration
{
    private const CARDS = [
        [
            'name' => "Qui a empoisonné le chef étoilé ?",
            'description' => "Le soir de l'inspection du guide gastronomique, le chef Gaston s'est effondré après avoir goûté sa propre sauce. Le flacon de sel contenait autre chose que du sel. Qui a fait le coup ?",
            'persons' => ["Le second de cuisine, qui rêvait de sa place", "Le critique gastronomique", "La plongeuse, renvoyée la veille", "Le fournisseur de truffes", "Le chef du restaurant d'en face", "Sa belle-mère, qui trouvait sa cuisine trop salée"],
            'words' => ["casserole", "étoile", "jalousie", "soufflé", "rancune", "tablier", "dégustation", "amertume", "brigade", "flambé", "vengeance", "menu"],
        ],
        [
            'name' => "Qui a assassiné le Père Noël ?",
            'description' => "Le 24 décembre au matin, le Père Noël a été retrouvé inanimé dans son atelier, la tête dans un sac de cadeaux. Il n'y aura peut-être pas de Noël cette année. Qui est le coupable ?",
            'persons' => ["La Mère Noël", "Le lutin en chef", "Rudolph, le renne au nez rouge", "Le Père Fouettard", "Un enfant qui n'a jamais eu son vélo", "Le Lapin de Pâques, jaloux de sa popularité"],
            'words' => ["traîneau", "cheminée", "chaussette", "rancœur", "lettre", "neige", "sapin", "liste", "sournois", "grelot", "réveillon", "oubli"],
        ],
        [
            'name' => "Qui a volé la couronne de la reine ?",
            'description' => "Pendant le bal du jubilé, les lumières du château se sont éteintes trente secondes. Quand elles se sont rallumées, la couronne avait disparu de la tête de la reine. Qui l'a dérobée ?",
            'persons' => ["Le prince héritier, pressé de régner", "Le majordome", "La dame de compagnie", "Le bouffon du roi", "Un ambassadeur étranger", "Le corgi de la reine"],
            'words' => ["diamant", "protocole", "trône", "révérence", "obscurité", "valse", "complot", "héritage", "sceptre", "mépris", "garde", "scandale"],
        ],
        [
            'name' => "Qui a poussé le gardien du phare ?",
            'description' => "Par une nuit de tempête, le vieux gardien du phare est tombé du haut de l'escalier. La lampe était éteinte et un bateau s'est échoué sur les rochers. Accident ou crime ?",
            'persons' => ["Son neveu, seul héritier", "Le capitaine du bateau échoué", "La sirène du port", "Le maire du village", "Un pirate à la retraite", "Le contrôleur des phares et balises"],
            'words' => ["tempête", "lanterne", "naufrage", "trésor", "brume", "escalier", "goéland", "épave", "testament", "marée", "isolement", "rhum"],
        ],
        [
            'name' => "Qui a saboté le spectacle de magie ?",
            'description' => "Le grand Zorbini devait sortir indemne d'une malle plongée dans l'eau. La malle est remontée vide, le lapin du magicien encore dedans. Où est passé Zorbini, et qui a trafiqué le tour ?",
            'persons' => ["Son assistante, sciée en deux tous les soirs", "Le lapin blanc", "Un magicien concurrent", "Le directeur du théâtre", "Un spectateur au premier rang", "Son ancien maître"],
            'words' => ["baguette", "disparition", "chapeau", "illusion", "cadenas", "trucage", "applaudissement", "secret", "rideau", "tricherie", "colombe", "abracadabra"],
        ],
        [
            'name' => "Qui a volé le gâteau du mariage ?",
            'description' => "À l'heure du dessert, la pièce montée de cinq étages a disparu de la salle des fêtes. Il ne reste qu'une traînée de crème jusqu'au parking. Qui a osé ?",
            'persons' => ["La mariée, au régime depuis six mois", "Le témoin du marié", "L'ex du marié, venue sans invitation", "Le traiteur", "Le petit cousin de 8 ans", "Le DJ"],
            'words' => ["chantilly", "alliance", "discours", "bouquet", "gourmandise", "cousin", "caramel", "dispute", "jarretière", "trahison", "champagne", "nougatine"],
        ],
        [
            'name' => "Qui a tué le comte Dracula ?",
            'description' => "Le comte a été retrouvé réduit en poussière dans son cercueil, un pieu en bois de chêne à côté de lui. Le château était pourtant fermé à double tour. Qui a fait le coup ?",
            'persons' => ["Van Helsing, le chasseur de vampires", "Igor, son fidèle serviteur", "La fiancée du comte", "Le loup-garou du village voisin", "Une chauve-souris jalouse", "Le marchand d'ail du marché"],
            'words' => ["cercueil", "crépuscule", "morsure", "miroir", "immortel", "chandelier", "malédiction", "ail", "crypte", "aube", "éternité", "cape"],
        ],
        [
            'name' => "Qui a kidnappé la mascotte du stade ?",
            'description' => "La veille de la finale, Gégé le coq, mascotte adorée du club, a disparu de son enclos. Sans lui, les joueurs refusent d'entrer sur le terrain. Qui l'a enlevé ?",
            'persons' => ["Le supporter du club adverse", "L'entraîneur, qui le trouvait trop bruyant", "Le capitaine de l'équipe", "L'arbitre", "Le vendeur de saucisses", "Le président du club, pour toucher l'assurance"],
            'words' => ["vestiaire", "pénalty", "rançon", "plume", "tribune", "sifflet", "défaite", "trophée", "écharpe", "rivalité", "maillot", "prolongation"],
        ],
        [
            'name' => "Qui a fait disparaître le professeur Tournesol ?",
            'description' => "Le savant devait présenter sa machine à voyager dans le temps devant l'Académie. Au moment de la démonstration, il ne restait de lui que ses lunettes fumantes. Qui a trafiqué l'invention ?",
            'persons' => ["Son assistant, qui signait toujours dans l'ombre", "Un espion d'une puissance étrangère", "Le président de l'Académie", "Sa femme, lassée des explosions", "Un voyageur venu du futur", "Le chat du laboratoire"],
            'words' => ["éprouvette", "formule", "explosion", "brevet", "chronomètre", "génie", "plagiat", "étincelle", "paradoxe", "prototype", "fumée", "démonstration"],
        ],
        [
            'name' => "Qui a assassiné le PDG de la start-up ?",
            'description' => "Le jour de l'entrée en bourse, le fondateur de la start-up a été retrouvé mort dans la salle de sieste, écrasé sous un pouf géant. Qui avait intérêt à le faire taire ?",
            'persons' => ["Son associé, qui a eu l'idée en premier", "Le stagiaire non payé", "L'investisseur principal", "Le développeur qui code la nuit", "La responsable du bonheur des salariés", "Le concurrent de la Silicon Valley"],
            'words' => ["licorne", "baby-foot", "algorithme", "actionnaire", "burn-out", "réunion", "piratage", "smoothie", "faillite", "ambition", "télétravail", "licenciement"],
        ],
        [
            'name' => "Qui a volé le Graal ?",
            'description' => "Après des années de quête, les chevaliers de la Table ronde avaient enfin rapporté le Graal à Camelot. Le lendemain matin, la coupe sacrée n'était plus sur son coussin. Qui l'a prise ?",
            'persons' => ["Le roi Arthur", "Merlin l'enchanteur", "La fée Morgane", "Lancelot du Lac", "Le cuisinier du château", "Un chevalier qui n'a jamais rien trouvé"],
            'words' => ["épée", "quête", "sortilège", "armure", "banquet", "félonie", "dragon", "potion", "tournoi", "prophétie", "écuyer", "honneur"],
        ],
        [
            'name' => "Qui a empoisonné la diva de l'opéra ?",
            'description' => "En plein grand air, la célèbre soprano Bianca Castafiore a perdu la voix et s'est écroulée sur scène. Son verre d'eau contenait du sirop pour la toux… et pas seulement. Qui est coupable ?",
            'persons' => ["Sa doublure", "Le chef d'orchestre", "Son agent", "Le ténor, éclipsé chaque soir", "Le souffleur caché sous la scène", "Une admiratrice éconduite"],
            'words' => ["partition", "vibrato", "loge", "rappel", "caprice", "bouquet", "fausse note", "orchestre", "jalousie", "costume", "entracte", "triomphe"],
        ],
        [
            'name' => "Qui a fait couler le paquebot ?",
            'description' => "Lors de sa croisière inaugurale, le paquebot de luxe Le Majestueux a heurté un iceberg… en pleine Méditerranée. Le gouvernail avait été bloqué. Qui a saboté le navire ?",
            'persons' => ["Le capitaine, couvert de dettes", "Le mécanicien en chef", "Une passagère de première classe", "Un passager clandestin", "L'armateur, pour l'assurance", "Le pianiste du salon"],
            'words' => ["hublot", "canot", "naufrage", "smoking", "compas", "assurance", "cabine", "iceberg", "sirène", "fortune", "mutinerie", "bouée"],
        ],
        [
            'name' => "Qui a cambriolé la banque de la ville ?",
            'description' => "Lundi matin, le coffre principal de la banque était vide, la porte intacte. Seul indice : une empreinte de chaussure de clown dans la poussière. Qui a fait le coup ?",
            'persons' => ["Le directeur de l'agence", "Le gardien de nuit", "Un clown du cirque de passage", "La caissière, sur le départ", "Un perceur de coffres à la retraite", "Le client le plus riche de la ville"],
            'words' => ["coffre-fort", "lingot", "tunnel", "alarme", "cagoule", "butin", "complice", "chèque", "évasion", "combinaison", "magot", "cavale"],
        ],
        [
            'name' => "Qui a tué le jardinier du château ?",
            'description' => "Le jardinier du domaine a été retrouvé au milieu du labyrinthe de buis, assommé par un nain de jardin. Ce matin-là, toutes les roses avaient été coupées. Qui est l'assassin ?",
            'persons' => ["La comtesse, propriétaire du domaine", "Le majordome (c'est toujours lui !)", "Le jardinier du château voisin", "La cuisinière", "Le fils du comte, joueur endetté", "Une taupe qui en avait assez"],
            'words' => ["sécateur", "labyrinthe", "rosier", "arrosoir", "héritage", "serre", "potager", "rivalité", "brouette", "épine", "concours", "parfum"],
        ],
        [
            'name' => "Qui a volé les dents de la grand-mère ?",
            'description' => "Au réveil, le dentier de Mamie Paulette avait disparu du verre posé sur la table de nuit. Le grand repas de famille est dans deux heures et elle refuse de descendre. Qui l'a pris ?",
            'persons' => ["Le chien de la famille", "Le petit-fils farceur", "Papi Robert, son mari depuis 60 ans", "L'aide-soignante", "Sa sœur jumelle, rivale de toujours", "La petite souris"],
            'words' => ["dentier", "sourire", "rôti", "pantoufle", "farce", "tricot", "sieste", "radotage", "verre", "mâchoire", "secret", "héritage"],
        ],
        [
            'name' => "Qui a fait exploser la fusée ?",
            'description' => "Trois, deux, un… boum ! La fusée devait emmener le premier touriste sur la Lune. Elle a explosé sur le pas de tir. Les enquêteurs ont retrouvé un briquet près des réservoirs. Qui est coupable ?",
            'persons' => ["Le milliardaire, propriétaire de la fusée", "L'astronaute, qui avait peur du vide", "L'ingénieur en chef", "Un milliardaire concurrent", "Un extraterrestre qui voulait la paix", "Le touriste, qui a lu le contrat trop tard"],
            'words' => ["compte à rebours", "apesanteur", "cratère", "combinaison", "réservoir", "étoile", "sabotage", "orbite", "décollage", "milliardaire", "lune", "panique"],
        ],
        [
            'name' => "Qui a assassiné le directeur de l'école ?",
            'description' => "Le jour de la remise des bulletins, le directeur a été retrouvé assommé dans son bureau, une pile de copies sur la tête. Le carnet de notes de la classe de CM2 a disparu. Qui a fait ça ?",
            'persons' => ["L'institutrice, épuisée", "Le cancre du fond de la classe", "Le premier de la classe", "La cantinière", "Le concierge", "La présidente des parents d'élèves"],
            'words' => ["cartable", "craie", "punition", "récréation", "bulletin", "tableau", "redoublement", "cantine", "colle", "devoirs", "tricherie", "sonnerie"],
        ],
        [
            'name' => "Qui a volé le trésor des pirates ?",
            'description' => "L'équipage du Hollandais Rieur a enfin déterré le coffre de Barbe Grise. En l'ouvrant, il ne contenait plus qu'un mot : « Merci ». Qui a trahi l'équipage ?",
            'persons' => ["Le capitaine à la jambe de bois", "Le perroquet du capitaine", "Le mousse", "Le cuisinier du navire", "Une sirène", "Le cartographe, qui a dessiné la carte"],
            'words' => ["carte", "boussole", "abordage", "rhum", "mutinerie", "doublon", "île", "trahison", "longue-vue", "planche", "squelette", "sabre"],
        ],
        [
            'name' => "Qui a empoisonné l'influenceuse ?",
            'description' => "En plein direct devant deux millions d'abonnés, la star des réseaux sociaux a goûté une nouvelle boisson détox… et s'est effondrée. Qui a glissé quelque chose dans son verre ?",
            'persons' => ["Sa meilleure amie, toujours dans l'ombre", "Son manager", "La marque de boissons sponsor", "Une influenceuse rivale", "Un abonné obsessionnel", "Son petit ami, filmé à son insu"],
            'words' => ["abonné", "filtre", "selfie", "partenariat", "jalousie", "commentaire", "détox", "buzz", "story", "hashtag", "harcèlement", "célébrité"],
        ],
        [
            'name' => "Qui a enlevé le chat de la voisine ?",
            'description' => "Minouche, le chat persan de Madame Michu, a disparu depuis mardi. Elle a reçu une lettre anonyme faite de lettres découpées dans le journal. Qui est le ravisseur ?",
            'persons' => ["Le facteur, griffé chaque matin", "Le voisin du dessus et son chien", "La concierge", "Le fils de Madame Michu", "Le vétérinaire", "Le chat de gouttière du quartier"],
            'words' => ["croquette", "griffure", "rançon", "pelote", "gouttière", "ronronnement", "lettre anonyme", "poubelle", "fugue", "voisinage", "ragots", "moustache"],
        ],
        [
            'name' => "Qui a assassiné le maître du château hanté ?",
            'description' => "Le vieux lord Ashford a convié six invités à passer la nuit dans son manoir. À minuit, la pendule a sonné treize coups et le lord a été retrouvé mort dans la bibliothèque. Qui est l'assassin ?",
            'persons' => ["Le fantôme de son arrière-grand-père", "Son notaire", "Sa nièce, unique héritière", "Le médium venu faire une séance", "La gouvernante", "Un explorateur revenu d'Égypte"],
            'words' => ["pendule", "bibliothèque", "testament", "chandelle", "passage secret", "fantôme", "orage", "momie", "poison", "grincement", "malédiction", "minuit"],
        ],
        [
            'name' => "Qui a truqué le concours de la plus grosse citrouille ?",
            'description' => "La citrouille du père Mathieu, favorite depuis des semaines, a été retrouvée percée de trous la veille de la pesée. Celle du gagnant était remplie de sable. Qui a triché ?",
            'persons' => ["Le gagnant du concours", "Le président du jury", "La femme du père Mathieu", "Un corbeau du champ voisin", "Le maraîcher bio du marché", "Le maire, qui voulait un gagnant de sa commune"],
            'words' => ["potager", "balance", "engrais", "médaille", "rivalité", "brouette", "fourche", "sable", "épouvantail", "triche", "récolte", "kermesse"],
        ],
        [
            'name' => "Qui a cassé la statue du musée ?",
            'description' => "Le matin de l'inauguration de l'exposition, la statue grecque vieille de 2 000 ans a été retrouvée en mille morceaux. Les caméras de surveillance ont été coupées à 3 h 12. Qui est coupable ?",
            'persons' => ["Le conservateur du musée", "Le gardien de nuit", "Une historienne de l'art", "Un collectionneur privé", "L'agent d'entretien et sa cireuse", "Le sculpteur qui a fait la copie"],
            'words' => ["marbre", "socle", "vitrine", "faussaire", "antiquité", "colle", "inauguration", "caméra", "assurance", "maladresse", "visiteur", "copie"],
        ],
        [
            'name' => "Qui a volé la recette secrète de la potion magique ?",
            'description' => "Le druide du village gaulois a découvert que son parchemin, avec la recette de la potion magique, avait disparu. Les Romains attaquent demain. Qui l'a volé ?",
            'persons' => ["Le barde, interdit de banquet", "Le forgeron", "Le poissonnier", "Un espion romain déguisé", "Le chef du village", "Le petit chien du livreur de menhirs"],
            'words' => ["chaudron", "gui", "sanglier", "menhir", "parchemin", "banquet", "serpe", "légionnaire", "bagarre", "druide", "poisson", "potion"],
        ],
    ];

    public function up()
    {
        $gameId = DB::table('gam_games')->where('name', 'Petits meurtres et faits divers')->value('id');
        if (!$gameId) {
            return;
        }

        $existing = DB::table('gam_cards')->where('game_id', $gameId)->pluck('name')->all();
        foreach (self::CARDS as $card) {
            if (in_array($card['name'], $existing)) {
                continue;
            }

            $description = [];
            foreach ($card['words'] as $i => $word) {
                $description['word'.($i + 1)] = $word;
            }
            $description['description'] = $card['description'];
            $description['persons'] = implode("\n", $card['persons']);

            DB::table('gam_cards')->insert([
                'game_id' => $gameId,
                'name' => $card['name'],
                'lang' => 'fr',
                'country' => '-',
                'status' => 1,
                'difficulty' => 1,
                'description' => json_encode($description),
                'created' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        $gameId = DB::table('gam_games')->where('name', 'Petits meurtres et faits divers')->value('id');
        DB::table('gam_cards')->where('game_id', $gameId)->whereIn('name', array_column(self::CARDS, 'name'))->delete();
    }
};
