<?php

namespace App\Repositories;

use App\Card;
use Mail;
use App;
use Auth;
use App\Providers\HelperServiceProvider;
use App\Repositories\UserRepository;
use DB;

class CardRepository implements ResourceRepositoryInterface
{

    protected $model;
	protected $userRepository;

    public function __construct(Card $Card, UserRepository $userRepository)
	{
		$this->model = $Card;
		$this->userRepository = $userRepository;
	}

	private function save(Card $Card, Array $inputs)
	{	
		if (isset($inputs['name'])){
			$Card->name = $inputs['name'];
		}
		
		if (isset($inputs['country'])){
			$Card->country = $inputs['country'];
		}
		
		if (isset($inputs['lang'])){
			$Card->lang = $inputs['lang'];
		}
		
		if (isset($inputs['description'])){
			$Card->description = $inputs['description'];
		}
		
		if (isset($inputs['game_id'])){
			$Card->game_id = $inputs['game_id'];
		}		
		
		$Card->save();	
		
	}

	public function getPaginate($n)
	{
		return $this->model->paginate($n);
	}

	public function store(Array $inputs)
	{
		$Card = new $this->model;
		
		$this->save($Card, $inputs);

		return $Card;
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
	
	public function getByGameId($id)
	{
		return $this->model->where("game_id","=",$id)->orderBy("name","desc")->get();
	}
	
	public function getForLangAndGame($lang,$game_id = 0){
		$o = $this->model->where("lang","=",$lang);
		if ($game_id!=0){
			$o = $o->where("game_id","=",$game_id);
		}
		$o = $o->orderBy("name","desc")->get();
		return $o;
	}
	
	
	public function getByCardId($sCard)
	{
		$o = $this->model->where("card_id","=",$sCard);
		$o = $o->get()->first();
		return $o;
	}
	
	public function checkDouble($lang,$game_id,$name){
		return $this->model->where("lang","=",$lang)->where("game_id","=",$game_id)->where('name', 'like', '%'.$name.'%')->orderBy("name","desc")->get();
	}
}