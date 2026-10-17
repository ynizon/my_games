<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * Suppression du jeu "Test" (inactif, sans carte), cree par la migration initiale des jeux.
 */
return new class extends Migration
{
    public function up()
    {
        $ids = DB::table('gam_games')->where('name', 'Test')->pluck('id');
        DB::table('gam_cards')->whereIn('game_id', $ids)->delete();
        DB::table('gam_games')->whereIn('id', $ids)->delete();
    }

    public function down()
    {
        DB::table('gam_games')->insert([
            'id' => 5,
            'name' => 'Test',
            'description' => '',
            'status' => 0,
            'nb' => 0,
        ]);
    }
};
