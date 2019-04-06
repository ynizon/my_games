<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Services\EmailRegisterValidator;

class EmailRegisterProvider extends ServiceProvider
{
	public function boot()
	{
		Validator::resolver(function($translator, $data, $rules, $messages)
		{
			return new EmailRegisterValidator($translator, $data, $rules, $messages);
		});
	}
}