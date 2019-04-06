<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gam_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',191)->default('');
			$table->string('nickname',191)->default('');
			$table->string('lang',191)->default('fr');
            $table->string('email',191)->unique();
            $table->string('password',191)->default('');
			$table->integer('status')->default(1);
			$table->date('logged_date')->default('1970-01-01');
            $table->rememberToken();
            $table->timestamps();
        });		
		
		// Insert 1 admin
		DB::table('gam_users')->insert(
			array(
				'email' => 'admin@admin.com',
				'name' => 'Admin',
				'lang' => 'fr',
				'nickname' => 'AD',
				'password'=>bcrypt("admin"),
				'status'=>1,
			)
		);
		
		// Insert 1 admin
		DB::table('gam_users')->insert(
			array(
				'email' => 'manager@manager.com',
				'name' => 'Manager',
				'lang' => 'fr',
				'nickname' => 'MA',
				'password'=>bcrypt("manager"),
				'status'=>1,
			)
		);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gam_users');
    }
}
