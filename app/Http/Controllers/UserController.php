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

class UserController extends Controller
{

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
		$this->userRepository = $userRepository;
	}

	public function index(Request $request)
    {
        //
		if (!auth::user()->can("user-edit")) {
			return view('errors/403',  array());
			exit();
		}
		
		$users = $this->userRepository->get();
		
		return view('user/index', compact('users'));
	}

	public function create()
	{
		if (!auth::user()->can("user-edit")) {
			return view('errors/403',  array());
			exit();
		}
		
		$users_roles = config('app.users_roles');
		
		return view('user/create',compact('users_roles'));
	}
		
	public function show($id)
	{
		return redirect('/user/'.$id."/edit");
	}

	public function edit($id)
	{
		if (!auth::user()->can("user-edit")) {
			return view('errors/403',  array());
			exit();
		}
		
		$users = $this->userRepository->get();
		$user = $this->userRepository->getById($id);
		$role = "";
		$json= json_decode($user->roles->first());
		if ($json != null){
			$role = $json->name;
		}
		$files = array();

		return view('user/edit',  compact('users','user','role'));
	}
	
	
	public function profile(Request $request)
	{	
	
		if (isset($request["password"])){
			//Modification du mot de passe
			$user = Auth::user();
			$this->userRepository->update($user->id, $request->all());

			return redirect('/')->withOk("Le mot de passe de " . $user->name . " a été mis à jour.");
		}else{
	
			$user = Auth::user();

			return view('user/profile',  compact('user'));
		}
	}

	public function update(UserUpdateRequest $request, $id)
	{
		$this->userRepository->update($id, $request->all());
		$user = $this->userRepository->getById($id);
		if (auth::user()->can("user-edit")) {
			return redirect('/')->withOk("L'utilisateur " . $request->input('name') . " a été modifié." );	
		}else{
			return redirect('/users')->withOk("L'utilisateur " . $request->input('name') . " a été modifié." );	
		}
	}

	public function destroy($id)
	{
		if (!auth::user()->can("user-edit")) {
			return view('errors/403',  array());
			exit();
		}
		
		$this->userRepository->destroy($id);
		return redirect()->back();
	}
	
	public function store(Request $request)
    {
		$user = Auth::user();
	
		try{
			$user = $this->userRepository->store($request->all());
			$user->save();
		
			if (!auth::user()->can("user-edit")) {
				return redirect('/')->withOk("L'utilisateur " . $request->input('name') . " a été créé." );
			}
		}catch(\Exception $e){
			return redirect('/users')->withError("L'utilisateur " . $request->input('name') . " n'a pas été créé pour la raison suivante: ".$e->getMessage() );
		}

    }
}