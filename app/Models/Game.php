<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\MyModel;

class Game extends MyModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'gam_games';
	public $timestamps = true;


}
