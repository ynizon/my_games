<?php

namespace App\Services;

use Illuminate\Validation\Validator;

class EmailRegisterValidator extends Validator
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function validateEmailDomain($attribute, $value, $parameters)
    {
		//Le mail doit appartenir au domaine
        $sEmail = strtolower($value);
		$tabEmails = explode("@",$sEmail);
		$r = false;
		
		if (isset($tabEmails[1])){
			if ($tabEmails[1]  == "search-foresight.com"){
				$r  = true;
			}
		}
		return $r;
    }

}