<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Recree la table des cartes a partir des fichiers database/xml,
 * sans toucher aux utilisateurs (contrairement a migrate:fresh).
 * Les cartes proposees par les utilisateurs (user_id renseigne) sont perdues : la commande refuse alors de continuer.
 */
class ReimportCards extends Command
{
    protected $signature = 'cards:reimport {--force : Ne pas demander de confirmation}';

    protected $description = 'Reimporte les cartes depuis les fichiers XML (database/xml)';

    public function handle()
    {
        $userCards = DB::table('gam_cards')->whereNotNull('user_id')->count();
        if ($userCards > 0) {
            $this->error($userCards . " carte(s) proposee(s) par des utilisateurs seraient perdues : abandon.");
            return self::FAILURE;
        }

        if (!$this->option('force') && !$this->confirm('La table des cartes va etre videe puis reimportee. Continuer ?')) {
            return self::FAILURE;
        }

        DB::transaction(function () {
            Schema::drop('gam_cards');
            require_once base_path('database/migrations/2017_03_08_151054_card.php');
            (new \Card())->up();
            Schema::table('gam_cards', function (Blueprint $table) {
                $table->integer('user_id')->nullable()->index()->after('game_id');
            });
            // Change la version du catalogue (nombre de cartes + derniere mise a jour) :
            // le site et l'application rechargent les cartes meme si leur nombre n'a pas bouge
            DB::table('gam_cards')->update(['created_at' => now(), 'updated_at' => now()]);
        });

        $rows = DB::table('gam_cards')->select('game_id', DB::raw('count(*) as total'))->groupBy('game_id')->get();
        $this->table(['Jeu', 'Cartes'], $rows->map(fn ($r) => [$r->game_id, $r->total])->all());

        return self::SUCCESS;
    }
}
