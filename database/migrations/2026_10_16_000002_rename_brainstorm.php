<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * "Brainstorm" devient "Checklist" (adresse /checklist ; /brainstorm est redirige, voir PlayController::OLD_SLUGS).
 */
return new class extends Migration
{
    public function up()
    {
        DB::table('gam_games')->where('name', 'Brainstorm')->update(['name' => 'Checklist', 'updated_at' => now()]);
    }

    public function down()
    {
        DB::table('gam_games')->where('name', 'Checklist')->update(['name' => 'Brainstorm', 'updated_at' => now()]);
    }
};
