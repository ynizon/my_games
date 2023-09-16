<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\GameRepository;
use Illuminate\Support\Facades\App;
use Session;
use Illuminate\Support\Facades\Auth;
use Mail;
class HomeController extends Controller
{

	protected UserRepository $usersRepository;
	protected GameRepository $gamesRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct( UserRepository $userRepository, GameRepository $gameRepository)
    {
		$this->userRepository = $userRepository;
		$this->gameRepository = $gameRepository;
        //$this->middleware('auth');
    }

	/**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function info(Request $request)
    {
		$games = $this->gameRepository->get();
		return view('info',compact('games'));
	}

	/**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		//Auth
		$user = Auth::user();
        $lang = config("app.locale");
		if ($user){
			if ($user->status != 1){
				Auth::logout();
				return redirect('/login')->withError("Votre compte a été suspendu." );
			}else{
				//On memorise sa date de connexion
				setcookie('locale', $user->lang);
				$user->logged_date = date("Y-m-d");
				$user->save();
			}
            $lang = $user->lang;
		}

		//Change lang
		if ($request->input("lang") != ""){
            $lang = $request->input("lang");
			$_COOKIE["locale"]=$request->input("lang");
			setcookie('locale',$request->input("lang"));
			if ($user){
				$user->lang = $request->input("lang");
				$user->save();
			}
			return redirect("/");
		}

		//Check locale
		if (!isset($_COOKIE["locale"])){
			$_COOKIE["locale"]=config("app.locale");
			setcookie('locale', config("app.locale"));
			return redirect("/");
		}else{
			$lang = $_COOKIE["locale"];
		}

		//Redirection
		if ($request->get("redirect") != null){
			return redirect($request->get("redirect"));
		}

		$games = $this->gameRepository->get();
        App::setLocale($lang);

		return view('index',compact('games','lang'));
	}

}
