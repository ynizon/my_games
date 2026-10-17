<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Game;
use App\Services\WerewolfDeck;
use Illuminate\Http\Request;

/**
 * Pages des jeux (reglages + partie), communes a tous les jeux.
 */
class PlayController extends Controller
{
    /**
     * url => [dossier des vues, parametres entiers lus dans la requete => [min, max]]
     * Les bornes correspondent aux listes proposees dans les pages de reglages.
     */
    public const GAMES = [
        self::TIMESUP => ['view' => 'timesup', 'params' => ['nbteams' => [2, 4]]],
        'checklist' => ['view' => 'brainstorm', 'params' => ['nbteams' => [2, 4], 'nbcards' => [2, 6]]],
        'croquisexpress' => ['view' => 'pictionary', 'params' => ['nbsets' => [1, 4], 'nbteams' => [2, 4]]],
        'motsinterdits' => ['view' => 'taboo', 'params' => ['nbsets' => [1, 4], 'nbteams' => [2, 4]]],
        self::WEREWOLF => ['view' => 'loupgarou', 'params' => ['nbplayers' => [8, 24], 'nbwolfs' => [2, 4]]],
    ];

    public const TIMESUP = 'lestroismanches';
    public const WEREWOLF = 'lesloupsduvillage';

    /** Anciennes adresses des jeux (avant leur renommage) => nouvelles, redirigees */
    public const OLD_SLUGS = [
        'timesup' => self::TIMESUP,
        'taboo' => 'motsinterdits',
        'brainstorm' => 'checklist',
        'pictionary' => 'croquisexpress',
        'loupgaroudethiercelieux' => self::WEREWOLF,
    ];

    protected WerewolfDeck $werewolfDeck;

    public function __construct(WerewolfDeck $werewolfDeck)
    {
        $this->werewolfDeck = $werewolfDeck;
    }

    public function index(Request $request, string $game)
    {
        $config = self::GAMES[$game];

        $locale = $_COOKIE["locale"] ?? null;
        if (!array_key_exists((string) $locale, config("app.langs"))) {
            setcookie('locale', config("app.locale"));
            return redirect("/".$game."/settings");
        }

        // Parametres absents ou hors bornes : retour aux reglages
        $data = array();
        foreach ($config['params'] as $param => [$min, $max]) {
            $value = filter_var($request->input($param), FILTER_VALIDATE_INT);
            if ($value === false || $value < $min || $value > $max) {
                return redirect("/".$game."/settings");
            }
            $data[$param] = $value;
        }

        if ($game == self::WEREWOLF) {
            $deck = $this->werewolfDeck->build($locale, $data['nbplayers'], $data['nbwolfs'], (array) $request->input("cards", array()));
            if ($deck === null) {
                return redirect("/".$game."/settings")->withError(__("messages.TooSpecialCards"));
            }
            $data = array_merge($data, $deck);
        }

        return view($config['view'].'/index', array_merge($data, $this->gameInfo($game)));
    }

    public function settings(string $game)
    {
        $data = $this->gameInfo($game);
        if ($game == self::WEREWOLF) {
            $locale = $_COOKIE["locale"] ?? config("app.locale");
            $data['cards'] = $this->werewolfDeck->selectableCards($locale);
        }

        return view(self::GAMES[$game]['view'].'/settings', $data);
    }

    /**
     * Adresse et nom affiche du jeu, pour les vues (le stockage du navigateur porte aussi l'adresse).
     */
    private function gameInfo(string $game): array
    {
        $model = Game::all()->first(fn ($item) => Helper::gameSlug($item->name) == $game);

        return ['gameSlug' => $game, 'gameName' => $model->name ?? $game];
    }

    /**
     * Ancienne adresse d'un jeu : redirection definitive vers la nouvelle (reglages et parametres conserves).
     */
    public function redirectOld(Request $request, string $old, string $suffix = '')
    {
        $query = $request->getQueryString();

        return redirect('/'.self::OLD_SLUGS[$old].$suffix.($query ? '?'.$query : ''), 301);
    }
}
