<?php

namespace App\Http\Controllers;

use App\Repositories\CardRepository;
use App\Repositories\GameRepository;
use Session;
use Illuminate\Http\Request;
use Auth;
use App\Card;
use Mail;
use DB;

class CardController extends Controller
{

    protected $cardRepository;

    public function __construct(GameRepository $gameRepository, CardRepository $cardRepository)
    {
		$this->cardRepository = $cardRepository;
		$this->gameRepository = $gameRepository;
	}

	public function index(Request $request)
    {
        //
		if (!auth::user()->can("card-edit")) {
			return view('errors/403',  array());
			exit();
		}
		
		$cards = $this->cardRepository->get();
		$gamestmp = $this->gameRepository->get();
		$games = array();
		foreach ($gamestmp as $game){
			$games[$game->id] = $game;
		}
		$game_id = (int) $request->get("game_id");
		return view('card/index', compact('game_id','cards','games'));
	}

	
	public function getall(Request $request)
    {
		$lang = $request->get("lang");
		$game_id = (int) $request->get("game_id");
        $cards = $this->cardRepository->getForLangAndGame($lang,$game_id);
		
		return response()->json($cards);
	}
	
	public function checkdouble(Request $request)
    {
		$lang = $request->get("lang");
		$game_id = (int) $request->get("game_id");
		$name = $request->get("name");
        $cards = $this->cardRepository->checkDouble($lang,$game_id,$name);
		
		return response()->json($cards);
	}
	
	public function create()
	{
		if (!auth::user()->can("card-edit")) {
			return view('errors/403',  array());
			exit();
		}
		
		$gamestmp = $this->gameRepository->get();
		$games = array();
		foreach ($gamestmp as $game){
			$games[$game->id] = $game->name;
		}
		
		return view('card/create',compact('games'));
	}
		
	public function show($id)
	{
		return redirect('/card/'.$id."/edit");
	}

	public function edit($id)
	{
		if (!auth::user()->can("card-edit")) {
			return view('errors/403',  array());
			exit();
		}
		
		$card = $this->cardRepository->getById($id);
		$gamestmp = $this->gameRepository->get();
		$games = array();
		foreach ($gamestmp as $game){
			$games[$game->id] = $game->name;
		}
		
		return view('card/edit',  compact('card','games'));
	}
	
	public function update(Request $request, $id)
	{
		if (!auth::user()->can("card-edit")) {
			return view('errors/403',  array());
			exit();
		}
		$this->cardRepository->update($id, $request->all());
		
		return redirect('/cards')->withOk("La carte " . $request->input('name') . " a été modifié." );	
		
	}

	public function destroy($id)
	{
		if (!auth::user()->can("card-edit")) {
			return view('errors/403',  array());
			exit();
		}
		
		$this->cardRepository->destroy($id);
		return redirect()->back();
	}
	
	public function store(Request $request)
    {
		if (!auth::user()->can("card-edit")) {
			return view('errors/403',  array());
			exit();
		}
		try{
			$card = $this->cardRepository->store($request->all());
			$card->save();
		
			return redirect('/cards')->withOk("La carte " . $request->input('name') . " a été créé." );
		}catch(\Exception $e){
			return redirect('/cards')->withError("La carte " . $request->input('name') . " n'a pas été créé pour la raison suivante: ".$e->getMessage() );
		}

    }
}