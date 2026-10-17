<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PlayController;
use App\Http\Resources\CardResource;
use App\Http\Resources\GameResource;
use App\Repositories\CardRepository;
use App\Repositories\GameRepository;
use App\Services\WerewolfDeck;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Jeux et cartes, en acces libre : l'application les telecharge
 * une fois pour jouer sans connexion.
 */
class CatalogController extends Controller
{
    protected GameRepository $gameRepository;
    protected CardRepository $cardRepository;
    protected WerewolfDeck $werewolfDeck;

    public function __construct(GameRepository $gameRepository, CardRepository $cardRepository, WerewolfDeck $werewolfDeck)
    {
        $this->gameRepository = $gameRepository;
        $this->cardRepository = $cardRepository;
        $this->werewolfDeck = $werewolfDeck;
    }

    public function config()
    {
        return response()->json([
            'langs' => config('app.langs'),
            'default_lang' => config('app.locale'),
            'countries' => config('app.countries'),
        ]);
    }

    /**
     * Jeux jouables (actifs et geres par l'application).
     */
    public function games()
    {
        $games = $this->gameRepository->get()->filter(function ($game) {
            return $game->status == 1 && array_key_exists(Helper::gameSlug($game->name), PlayController::GAMES);
        });

        return GameResource::collection($games->values());
    }

    /**
     * Version des cartes d'une langue : a comparer avec celle gardee par l'application
     * pour savoir s'il faut tout retelecharger.
     */
    public function cardsVersion(Request $request)
    {
        $lang = $this->lang($request);

        return response()->json(['lang' => $lang, 'version' => Helper::cardsVersion($lang)]);
    }

    /**
     * Toutes les cartes validees d'une langue (eventuellement d'un seul jeu).
     */
    public function cards(Request $request)
    {
        $lang = $this->lang($request);
        $request->validate(['game_id' => 'nullable|integer']);
        $cards = $this->cardRepository->getForLangAndGame($lang, (int) $request->input('game_id'));

        return CardResource::collection($cards)->additional([
            'meta' => ['lang' => $lang, 'version' => Helper::cardsVersion($lang)],
        ]);
    }

    /**
     * Cartes du Loup-Garou avec leurs regles de distribution : role (villager, wolf ou special,
     * seuls les "special" se choisissent dans les reglages), copies et is_wolf.
     * L'application peut ainsi distribuer les roles sans connexion.
     */
    public function werewolfCards(Request $request)
    {
        return CardResource::collection($this->werewolfDeck->cards($this->lang($request)));
    }

    /**
     * Distribution des roles du Loup-Garou, dans un ordre aleatoire (une carte par joueur).
     */
    public function werewolfDeck(Request $request)
    {
        $params = PlayController::GAMES[PlayController::WEREWOLF]['params'];
        $attributes = $request->validate([
            'nbplayers' => 'required|integer|between:'.implode(',', $params['nbplayers']),
            'nbwolfs' => 'required|integer|between:'.implode(',', $params['nbwolfs']),
            'cards' => 'array',
            'cards.*' => 'integer',
        ]);

        $deck = $this->werewolfDeck->build(
            $this->lang($request),
            $attributes['nbplayers'],
            $attributes['nbwolfs'],
            $attributes['cards'] ?? array()
        );
        if ($deck === null) {
            return response()->json(['message' => __("messages.TooSpecialCards")], 422);
        }

        shuffle($deck['deck']);
        $deck['deck'] = array_map(function ($card) {
            $card['image'] = url($card['image']);
            return $card;
        }, $deck['deck']);

        return response()->json($deck);
    }

    private function lang(Request $request): string
    {
        $request->validate([
            'lang' => ['nullable', Rule::in(array_keys(config('app.langs')))],
        ]);

        return $request->input('lang', config('app.locale'));
    }
}
