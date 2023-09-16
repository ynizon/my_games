<?php

namespace App\Http\Controllers;
use App\Repositories\CardRepository;
use App\Repositories\UserRepository;
use Session;
use Illuminate\Http\Request;
use Auth;
use Mail;
use DB;

class LoupGarouController extends Controller
{

    protected $userRepository;
	protected $cardRepository;

    public function __construct(UserRepository $userRepository,CardRepository $cardRepository)
    {
		$this->userRepository = $userRepository;
		$this->cardRepository = $cardRepository;
	}

	public function index(Request $request)
    {
		if (!isset($_COOKIE["locale"])){
			setcookie('locale', config("app.locale"));
			return redirect("/loupgarou/settings");
		}
     	$nbplayers = (int) $request->input("nbplayers");
		$nbwolfs = (int) $request->input("nbwolfs");
		$cardsid = $request->input("cards");
		$cards = $this->cardRepository->getByGameId(4);
		return view('loupgarou/index',compact('cards','nbwolfs','nbplayers','cardsid'));
	}

	public function settings(Request $request)
    {
		$cards = $this->cardRepository->getByGameId(4);
		return view('loupgarou/settings', compact("cards"));
	}

}
