<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $nbGames = Game::count();
        $games= Game::all();
        $nbUsers = User::count();
        $nbCards = Card::count();
        $nbWaitingCards = Card::where("status","=","0")->count();
        $waitingCards = Card::where("status","=",0)->limit(10)->get();

        return view('dashboard.index', compact("nbGames","games","nbCards","nbUsers","nbWaitingCards", "waitingCards"));
    }

    public function games() {
        $games= Game::all();
        return view('dashboard.games', compact("games"));
    }

    public function cards() {
        $cards= Card::all();
        return view('dashboard.cards', compact("cards"));
    }
}
