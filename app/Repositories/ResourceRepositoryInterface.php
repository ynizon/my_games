<?php

namespace App\Repositories;

interface ResourceRepositoryInterface
{

	public function store(Array $inputs);
	public function update($id, Array $inputs);
	public function destroy($id);
}