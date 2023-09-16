<?php

namespace App\Http\Controllers;

use App\Repositories\GameRepository;
use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Mail;
use DB;

class GameController extends Controller
{
    protected GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
		$this->gameRepository = $gameRepository;
	}

	public function index(Request $request)
    {
        $this->checkPermission();

		$games = $this->gameRepository->get();

		return view('game/index', compact('games'));
	}


	public function getall(Request $request)
    {
		$lang = $request->get("lang");
		$game_id = (int) $request->get("game_id");
        $games = $this->gameRepository->get();

		return Response::json($games);
	}

	public function create()
	{
        $this->checkPermission();

		return view('game/create');
	}

	public function show($id)
	{
		return redirect('/game/'.$id."/edit");
	}

	public function edit($id)
	{
        $this->checkPermission();

		$game = $this->gameRepository->getById($id);

		return view('game/edit',  compact('game'));
	}

	public function update(Request $request, $id)
	{
        $this->checkPermission();
		$this->gameRepository->update($id, $request->all());

		return redirect('/games')->withOk("Le jeu " . $request->input('name') . " a été modifié." );
	}

	public function destroy($id)
	{
        $this->checkPermission();

		$this->gameRepository->destroy($id);
		return redirect()->back();
	}

	public function store(Request $request)
    {
        $this->checkPermission();
		try{
			$game = $this->gameRepository->store($request->all());
			$game->save();

			return redirect('/games')->withOk("Le jeu " . $request->input('name') . " a été créé." );
		}catch(\Exception $e){
			return redirect('/games')->withError("Le jeu " . $request->input('name') . " n'a pas été créé pour la raison suivante: ".$e->getMessage() );
		}
    }

    private function checkPermission() {
        if (!Auth::user()->can("game-edit")) {
            return view('errors/403',  array());
        }
    }
}
