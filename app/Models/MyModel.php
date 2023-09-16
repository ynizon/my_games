<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class MyModel extends Model
{

	public static function boot() {
		   parent::boot();

	// create a event to happen on updating
	   static::updating(function($table)  {
		   if (Auth::user() != null){
				$table->updated_by = Auth::user()->name;
		   }
	   });

	// create a event to happen on deleting
	   static::deleting(function($table)  {
		   if (Auth::user() != null){
			$table->deleted_by = Auth::user()->name;
		   }
	   });

	// create a event to happen on saving
	   static::saving(function($table)  {
		   if (Auth::user() != null){
				$table->created_by = Auth::user()->name;
		   }

	   });
	}

}
