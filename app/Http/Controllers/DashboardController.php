<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Les statistiques sont reservees aux gestionnaires des cartes ;
        // un simple utilisateur est renvoye vers ses propositions.
        if (!Auth::user()->can("card-edit")) {
            return redirect()->route('cards');
        }

        $nbGames = Game::count();
        $games = Game::withCount([
            'cards',
            'cards as waiting_cards_count' => function ($query) {
                $query->where('status', '=', 0);
            },
        ])->get();
        $nbUsers = User::count();
        $nbCards = Card::count();
        $nbWaitingCards = Card::where("status", "=", 0)->count();
        $waitingCards = Card::where("status", "=", 0)->limit(10)->get();

        return view('dashboard.index', compact("nbGames", "games", "nbCards", "nbUsers", "nbWaitingCards", "waitingCards"));
    }
}
