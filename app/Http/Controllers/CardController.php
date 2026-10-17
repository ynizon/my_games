<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardRequest;
use App\Models\Card;
use App\Repositories\CardRepository;
use App\Repositories\GameRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CardController extends Controller
{
    protected CardRepository $cardRepository;
    protected GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository, CardRepository $cardRepository)
    {
        $this->cardRepository = $cardRepository;
        $this->gameRepository = $gameRepository;
    }

    public function index(Request $request)
    {
        $this->checkPermission();

        $cards = $this->cardRepository->getMyCards();
        $games = $this->gameRepository->get()->keyBy('id');
        $game_id = (int) $request->get("game_id");
        return view('card/index', compact('game_id', 'cards', 'games'));
    }

    public function getall(Request $request)
    {
        $lang = $request->get("lang");
        $game_id = (int) $request->get("game_id");
        $cards = $this->cardRepository->getForLangAndGame($lang, $game_id);

        return response()->json($cards);
    }

    public function checkdouble(Request $request)
    {
        $lang = $request->get("lang");
        $game_id = (int) $request->get("game_id");
        $name = $request->get("name");
        $cards = $this->cardRepository->checkDouble($lang, $game_id, $name);

        return response()->json($cards);
    }

    public function create(Request $request)
    {
        $this->checkPermission();

        $games = $this->gameRepository->get()->pluck('name', 'id');
        $game_id = $request->input("game_id");

        return view('card/create', compact('games', 'game_id'));
    }

    public function show($id)
    {
        return redirect('/cards/'.$id."/edit");
    }

    public function edit($id)
    {
        $card = $this->cardRepository->getById($id);
        $this->checkPermission($card);
        $games = $this->gameRepository->get()->pluck('name', 'id');

        return view('card/edit', compact('card', 'games'));
    }

    public function update(CardRequest $request, $id)
    {
        $card = $this->cardRepository->getById($id);
        $this->checkPermission($card);

        $this->cardRepository->update($id, $this->inputs($request));
        $card->refresh();

        return redirect('/cards?game_id='.$card->game_id)
            ->withOk("La carte " . $card->name . " a été modifiée.");
    }

    public function destroy($id)
    {
        $this->checkPermission($this->cardRepository->getById($id));

        $this->cardRepository->destroy($id);
        return redirect()->back();
    }

    public function store(CardRequest $request)
    {
        $this->checkPermission();

        try {
            $card = $this->cardRepository->store($this->inputs($request));

            return redirect('/cards?game_id='.$card->game_id)->withOk("La carte " . $card->name . " a été créée.");
        } catch (\Exception $e) {
            Log::error($e);
            return redirect('/cards')->withError("La carte " . $request->input('name') . " n'a pas pu être créée.");
        }
    }

    /**
     * Champs valides du formulaire. Une carte proposee par un utilisateur
     * repasse en attente de validation (status = 0).
     */
    private function inputs(CardRequest $request): array
    {
        $inputs = $request->validated();
        if (!Auth::user()->can("card-edit")) {
            $inputs['status'] = 0;
        }

        return $inputs;
    }

    /**
     * card-edit : gere toutes les cartes ; card-propose : uniquement les siennes.
     */
    private function checkPermission(?Card $card = null)
    {
        $user = Auth::user();
        if ($user->can("card-edit")) {
            return;
        }

        if (!$user->can("card-propose")) {
            abort(403);
        }

        if ($card !== null && $card->user_id != $user->id) {
            abort(403);
        }
    }
}
