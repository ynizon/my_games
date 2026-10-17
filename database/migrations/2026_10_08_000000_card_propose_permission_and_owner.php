<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * - Le role User ne gere plus toutes les cartes (card-edit) :
 *   il peut seulement proposer les siennes (card-propose), validees ensuite par un admin.
 * - Les cartes sont rattachees a leur auteur par user_id (le nom n'est pas unique).
 */
return new class extends Migration
{
    public function up()
    {
        $roleUserId = DB::table('gam_roles')->where('name', 'User')->value('id');
        $cardEditId = DB::table('gam_permissions')->where('name', 'card-edit')->value('id');

        $cardProposeId = DB::table('gam_permissions')->where('name', 'card-propose')->value('id');
        if (!$cardProposeId) {
            $cardProposeId = DB::table('gam_permissions')->insertGetId(array(
                'name' => 'card-propose',
                'display_name' => 'card-propose',
                'description' => "Proposition de cartes",
            ));
        }

        if ($roleUserId) {
            DB::table('gam_permission_role')
                ->where('role_id', $roleUserId)
                ->where('permission_id', $cardEditId)
                ->delete();

            $exists = DB::table('gam_permission_role')
                ->where('role_id', $roleUserId)
                ->where('permission_id', $cardProposeId)
                ->exists();
            if (!$exists) {
                DB::table('gam_permission_role')->insert(array(
                    'permission_id' => $cardProposeId,
                    'role_id' => $roleUserId,
                ));
            }
        }

        Schema::table('gam_cards', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->index()->after('game_id');
        });

        // Rattache les cartes existantes a leur auteur quand le nom est sans ambiguite
        $users = DB::table('gam_users')->select('id', 'name')->get()->groupBy('name');
        foreach ($users as $name => $homonyms) {
            if ($name !== "" && count($homonyms) == 1) {
                DB::table('gam_cards')->where('created_by', $name)->update(['user_id' => $homonyms[0]->id]);
            }
        }
    }

    public function down()
    {
        Schema::table('gam_cards', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        $roleUserId = DB::table('gam_roles')->where('name', 'User')->value('id');
        $cardEditId = DB::table('gam_permissions')->where('name', 'card-edit')->value('id');
        $cardProposeId = DB::table('gam_permissions')->where('name', 'card-propose')->value('id');

        DB::table('gam_permission_role')->where('permission_id', $cardProposeId)->delete();
        DB::table('gam_permissions')->where('id', $cardProposeId)->delete();
        if ($roleUserId && $cardEditId) {
            DB::table('gam_permission_role')->insert(array(
                'permission_id' => $cardEditId,
                'role_id' => $roleUserId,
            ));
        }
    }
};
