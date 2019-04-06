<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EntrustSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::create('gam_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('gam_role_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('gam_users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('gam_roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        // Create table for storing permissions
        Schema::create('gam_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('gam_permission_role', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('permission_id')->references('id')->on('gam_permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('gam_roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });
		
		/* Roles */
		DB::table('gam_roles')->insert(
			array(
				'name' => 'Admin',
				'display_name'=>'Administrator',				
				'description'=>"Tous les droits",
			)
		);
		DB::table('gam_roles')->insert(
			array(
				'name' => 'Manager',
				'display_name'=>'Manager',				
				'description'=>"Edit cards",
			)
		);	
		DB::table('gam_roles')->insert(
			array(
				'name' => 'User',
				'display_name'=>'User',				
				'description'=>"User",
			)
		);
		
		//Permissions
		DB::table('gam_permissions')->insert(
			array(
				'name' => 'user-edit',
				'display_name'=>'user-edit',
				'description'=>"Gestion des utilisateurs",
			)
		);
		
		//Permissions
		DB::table('gam_permissions')->insert(
			array(
				'name' => 'card-edit',
				'display_name'=>'card-edit',
				'description'=>"Gestion des cartes",
			)
		);
		
		//Permissions
		DB::table('gam_permissions')->insert(
			array(
				'name' => 'game-edit',
				'display_name'=>'game-edit',
				'description'=>"Gestion des jeux",
			)
		);
		
		//Pour les admins
		DB::table('gam_permission_role')->insert(
			array(
				'permission_id' => 1,
				'role_id'=>1
			)
		);
		
		DB::table('gam_permission_role')->insert(
			array(
				'permission_id' => 2,
				'role_id'=>1
			)
		);
		
		DB::table('gam_permission_role')->insert(
			array(
				'permission_id' =>3,
				'role_id'=>1
			)
		);
		
		//Manager
		DB::table('gam_permission_role')->insert(
			array(
				'permission_id' =>2,
				'role_id'=>2
			)
		);
		
		//Personne ADMIN
		DB::table('gam_role_user')->insert(
				array(
					'user_id' => 1,
					'role_id'=>1
				)
			);
			
		//Personne Manager
		DB::table('gam_role_user')->insert(
				array(
					'user_id' => 2,
					'role_id'=>2
				)
			);
		
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::drop('gam_permission_role');
        Schema::drop('gam_permissions');
        Schema::drop('gam_role_user');
        Schema::drop('gam_roles');
    }
}
