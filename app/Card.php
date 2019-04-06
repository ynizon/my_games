<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\MyModel;

class Card extends MyModel 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'gam_cards';
	public $timestamps = true;
	
	
}
