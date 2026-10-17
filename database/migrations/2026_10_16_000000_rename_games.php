<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * Les jeux prennent des noms propres au site : les noms d'origine sont des marques deposees.
 * L'adresse de chaque jeu (et son stockage dans le navigateur) est derivee de son nom :
 * les anciennes adresses sont redirigees (voir PlayController::OLD_SLUGS).
 */
return new class extends Migration
{
    private const NAMES = [
        "Time's up" => "Les Trois Manches",
        "Taboo" => "Mots Interdits",
        "Pictionary" => "Croquis Express",
        "Loup Garou de Thiercelieux" => "Les Loups du Village",
    ];

    public function up()
    {
        foreach (self::NAMES as $old => $new) {
            DB::table('gam_games')->where('name', $old)->update(['name' => $new, 'updated_at' => now()]);
        }
    }

    public function down()
    {
        foreach (self::NAMES as $old => $new) {
            DB::table('gam_games')->where('name', $new)->update(['name' => $old, 'updated_at' => now()]);
        }
    }
};
