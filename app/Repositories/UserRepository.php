<?php

namespace App\Repositories;

use App;
use App\Models\User;
use Auth;
use DB;

class UserRepository implements ResourceRepositoryInterface
{

    protected $model;

    public function __construct(User $user)
	{
		$this->model = $user;
	}

	private function save(User $user, Array $inputs)
	{
		if (isset($inputs['name'])){
			$user->name = $inputs['name'];
		}

		if (isset($inputs['nickname'])){
			$user->nickname = $inputs['nickname'];
		}

		if (isset($inputs['email'])){
			$user->email = $inputs['email'];
		}

		if (isset($inputs['status'])){
			$user->status = $inputs['status'];
		}

		if (isset($inputs['password'])){
			if ($inputs['password'] != ""){
				$user->password = bcrypt($inputs['password']);
			}
		}
		$user->save();

		//Verif qu il y ai au moins un role
		$lst = DB::select("select count(user_id) as nb from gam_role_user where user_id=?",array($user->id));
		foreach ($lst as $o){
			if ($o->nb == 0){
				$user->roles()->attach(3);//Role user par defaut
			}
		}

		//Update des roles
		if (isset($inputs['role'])){
			$lst = DB::select("select id,name from gam_roles",array());
			foreach ($lst as $o){
				if ($user->hasRole($o->name)){
					$user->roles()->detach($o->id);
				}
				if ($inputs['role'] == $o->name){
					$user->roles()->attach($o->id);
				}
			}
		}
	}

	public function getPaginate($n)
	{
		$user = Auth::user();
		return $this->model->paginate($n);
	}

	public function store(Array $inputs)
	{
		$user = new $this->model;

		$this->save($user, $inputs);

		return $user;
	}

	public function getById($id)
	{
		$oUser = $this->model->findOrFail($id);

		return $oUser;
	}

	public function getByName($name)
	{
		return $this->model->where("name","=",$name)->get();

	}

	public function update($id, Array $inputs)
	{
		$this->save($this->getById($id), $inputs);
	}

	public function destroy($id)
	{
		$this->getById($id)->delete();
	}

	public function get()
	{
		$usersTmp = $this->model->OrderBy("name")->get();
		$users = array();
		foreach ($usersTmp as $user){
			$users[$user->id] = $user;
		}
		return $users;

	}


	/* Renvoie les admins actifs */
	public function getAdmins()
	{
		$lst = $this->model->where("status","=","1")->OrderBy("name")->get();
		$users = array();
		foreach ($lst as $user ){
			if ($user->hasRole("Admin")){
				$users[] = $user;
			}
		}
		return $users;

	}

	public function getActif()
	{
		$users = $this->model->where("status","=","1")->OrderBy("name")->get();

		return $users;

	}

}
