<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\UserRepository;
use Session;
use Illuminate\Http\Request;
use Auth;
use App\User;
use Mail;
use DB;

class TimesupController extends Controller
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
			return redirect("/timesup/settings");
		}
		
     	$nbteams = (int) $request->input("nbteams");
		return view('timesup/index',compact('nbteams'));
	}

	public function settings(Request $request)
    {		
		return view('timesup/settings');
	}

}