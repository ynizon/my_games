<?php

namespace App\Repositories;

abstract class ResourceRepository
{

    protected $model;

    public function getPaginate($n)
	{
		return $this->model->paginate($n);
	}
	
	public function getCount()
	{
		return $this->model->count();
	}
	
	public function store(Array $inputs)
	{
		return $this->model->create($inputs);
	}

	public function getById($id)
	{
		return $this->model->findOrFail($id);
	}

	public function update($id, Array $inputs)
	{
		$this->getById($id)->fill($inputs)->save();
	}

	public function destroy($id)
	{
		$this->getById($id)->delete();
	}

}