<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Session;
use Illuminate\Http\Request;
use Auth;
use Mail;
use DB;

class BrainstormController extends Controller
{

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
		$this->userRepository = $userRepository;
	}

	public function index(Request $request)
    {
		if (!isset($_COOKIE["locale"])){
			setcookie('locale', config("app.locale"));
			return redirect("/brainstorm/settings");
		}
     	$nbteams = (int) $request->input("nbteams");
		$nbcards = (int) $request->input("nbcards");
		return view('brainstorm/index',compact('nbteams','nbcards'));
	}

	public function settings(Request $request)
    {
		return view('brainstorm/settings');
	}

}
