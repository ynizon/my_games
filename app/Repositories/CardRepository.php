<?php

namespace App\Repositories;

use App\Models\Card;
use Illuminate\Support\Facades\Auth;

class CardRepository implements ResourceRepositoryInterface
{

    protected $model;

    public function __construct(Card $Card)
    {
        $this->model = $Card;
    }

    private function save(Card $Card, array $inputs)
    {
        foreach (['name', 'country', 'lang', 'description', 'game_id', 'difficulty', 'status'] as $field) {
            if (isset($inputs[$field])) {
                $Card->$field = $inputs[$field];
            }
        }

        $Card->save();
    }

    public function getPaginate($n)
    {
        return $this->model->paginate($n);
    }

    public function store(array $inputs)
    {
        $Card = new $this->model;
        $Card->description = "";

        $this->save($Card, $inputs);

        return $Card;
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function update($id, array $inputs)
    {
        $this->save($this->getById($id), $inputs);
    }

    public function destroy($id)
    {
        $this->getById($id)->delete();
    }

    public function get()
    {
        return $this->model->orderBy("name", "desc")->get();
    }

    public function getMyCards()
    {
        $user = Auth::user();
        $query = $this->model->with('game')->orderBy("name", "desc");
        if (!$user->can("card-edit")) {
            $query->where("user_id", "=", $user->id);
        }
        return $query->get();
    }

    public function getByGameId($id)
    {
        return $this->model->where("game_id", "=", $id)->orderBy("name", "desc")->get();
    }

    public function getForLangAndGame($lang, $game_id = 0)
    {
        $o = $this->model->where("lang", "=", $lang)->where("status", "=", 1);
        if ($game_id!=0) {
            $o = $o->where("game_id", "=", $game_id);
        }
        $o = $o->orderBy("name", "desc")->get();
        return $o;
    }

    public function getByCardId($sCard)
    {
        $o = $this->model->where("card_id", "=", $sCard);
        $o = $o->get()->first();
        return $o;
    }

    public function checkDouble($lang, $game_id, $name)
    {
        $search = addcslashes((string) $name, '\%_');

        return $this->model->where("lang", "=", $lang)->where("game_id", "=", $game_id)
            ->where('name', 'like', '%'.$search.'%')->orderBy("name", "desc")->get();
    }
}
