<?php

namespace App\Repositories;

use App\Models\Game;
use Mail;
use App;
use Auth;
use DB;

class GameRepository implements ResourceRepositoryInterface
{

    protected $model;
	protected $userRepository;

    public function __construct(Game $Game, UserRepository $userRepository)
	{
		$this->model = $Game;
		$this->userRepository = $userRepository;
	}

	private function save(Game $Game, Array $inputs)
	{
		if (isset($inputs['name'])){
			$Game->name = $inputs['name'];
		}

		if (isset($inputs['description'])){
			$Game->description = $inputs['description'];
		}

		if (isset($inputs['status'])){
			$Game->status = $inputs['status'];
		}

		$Game->save();

	}

	public function getPaginate($n)
	{
		return $this->model->paginate($n);
	}

	public function store(Array $inputs)
	{
		$Game = new $this->model;

		$this->save($Game, $inputs);

		return $Game;
	}

	public function getById($id)
	{
		return $this->model->findOrFail($id);
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
		return $this->model->orderBy("name","desc")->get();
	}

	public function getActive()
	{
		return $this->model->where("status","=","1")->orderBy("name","desc")->get();
	}

}
