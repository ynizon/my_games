<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Repositories\CardRepository;
use App\Repositories\GameRepository;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use DB;

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
        $gamestmp = $this->gameRepository->get();
        $games = array();
        foreach ($gamestmp as $game) {
            $games[$game->id] = $game;
        }
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

        $gamestmp = $this->gameRepository->get();
        $games = array();
        foreach ($gamestmp as $game) {
            $games[$game->id] = $game->name;
        }
        $game_id = $request->input("game_id");

        return view('card/create', compact('games', 'game_id'));
    }

    public function show($id)
    {
        return redirect('/card/'.$id."/edit");
    }

    public function edit($id)
    {
        $card = $this->cardRepository->getById($id);
        $this->checkPermission($card);
        $gamestmp = $this->gameRepository->get();
        $games = array();
        foreach ($gamestmp as $game) {
            $games[$game->id] = $game->name;
        }

        return view('card/edit', compact('card', 'games'));
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission();
        $user = Auth::user();
        $this->cardRepository->update($id, $request->all());
        $card = Card::find($id);
        if (!$user->can("card-edit")) {
            $card->status = 0;
            $card->save();
        }

        return redirect('/cards?game_id='.$card->game_id)
            ->withOk("La carte " . $request->input('name') . " a été modifié.");

    }

    public function destroy($id)
    {
        $this->checkPermission();

        $this->cardRepository->destroy($id);
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $this->checkPermission();

        try {
            $card = $this->cardRepository->store($request->all());
            if (!$user->can("card-edit")) {
                $card->status = 0;
            }
            $card->save();

            return redirect('/cards?game_id='.$card->game_id)->withOk("La carte " . $request->input('name') . " a été créé.");
        } catch(\Exception $e) {
            return redirect('/cards')->withError("La carte " . $request->input('name') . " n'a pas été créé pour la raison suivante: ".$e->getMessage());
        }
    }

    private function checkPermission($card = null)
    {
        if (!Auth::user()->can("card-edit")) {
            abort(403);
        }

        if (!empty($card)){
            if (Auth::user()->hasRole("user")) {
                if ($card->created_by != Auth::user()->name) {
                    abort(403);
                }
            }
        }
    }
}
