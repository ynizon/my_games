<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Session;
use Illuminate\Http\Request;
use Auth;
use Mail;
use DB;

class PictionaryController extends Controller
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
			return redirect("/pictionary/settings");
		}
		$nbsets = (int) $request->input("nbsets");
     	$nbteams = (int) $request->input("nbteams");
		return view('pictionary/index',compact('nbteams','nbsets'));
	}

	public function settings(Request $request)
    {
		return view('pictionary/settings');
	}

}
