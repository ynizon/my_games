<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UserUpdateRequest extends Request
{

    public function authorize()
	{
		return true;
	}

	public function rules()
	{
		$id = $this->segment(2);
		return [
			'name' => 'required|max:255|unique:gam_users,name,' . $id,
			'email' => 'required|email|max:255|unique:gam_users,email,' . $id
		];
	}

}