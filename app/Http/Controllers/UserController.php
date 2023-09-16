<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use DB;

class UserController extends Controller
{

    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
		$this->userRepository = $userRepository;
	}

	public function index(Request $request)
    {
        $this->checkPermission();

		$users = $this->userRepository->get();

		return view('user/index', compact('users'));
	}

	public function create()
	{
        $this->checkPermission();

		$users_roles = config('app.users_roles');

		return view('user/create',compact('users_roles'));
	}

	public function show($id)
	{
		return redirect('/user/'.$id."/edit");
	}

	public function edit($id)
	{
        $this->checkPermission();

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
        $user = Auth::user();
		if (isset($request["password"])){
			//Modification du mot de passe
			$this->userRepository->update($user->id, $request->all());

			return redirect('/')->withOk("Le mot de passe de " . $user->name . " a été mis à jour.");
		}else{
			return view('user/profile',  compact('user'));
		}
	}

	public function update(Request $request, $id)
	{
		$this->userRepository->update($id, $request->all());
		$user = $this->userRepository->getById($id);
        if (!Auth::user()->can("user-edit")) {
			return redirect('/')->withOk("L'utilisateur " . $request->input('name') . " a été modifié." );
		}else{
			return redirect('/users')->withOk("L'utilisateur " . $request->input('name') . " a été modifié." );
		}
	}

	public function destroy($id)
	{
		$this->checkPermission();

		$this->userRepository->destroy($id);
		return redirect()->back();
	}

	public function store(Request $request)
    {
        $this->checkPermission();
		try{
            $user = $this->userRepository->store($request->all());
            $user->save();

            return redirect('/users')->withOk("L'utilisateur " . $request->input('name') . " a été créé." );
		}catch(\Exception $e){
			return redirect('/users')->withError("L'utilisateur " . $request->input('name') . " n'a pas été créé pour la raison suivante: ".$e->getMessage() );
		}

    }

    private function checkPermission() {
        if (!Auth::user()->can("user-edit")) {
            return view('errors/403',  array());
        }
    }
}
