<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Game extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('gam_games', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name')->default("");
			$table->integer('status')->default(1);
			$table->text('description');
			$table->timestamps();
			$table->string('created_by')->default("");
			$table->string('updated_by')->default("");
			$table->string('deleted_by')->default("");
		});

		
		DB::table('gam_games')->insert(
			array(
				'name' => "Time's up",
				'description' => "",
				'status'=>1
			)
		);  
		
		DB::table('gam_games')->insert(
			array(
				'name' => "Brainstorm",
				'description' => "",
				'status'=>1
			)
		);  
		
		DB::table('gam_games')->insert(
			array(
				'name' => "Taboo",
				'description' => "",
				'status'=>1
			)
		);  
		
		DB::table('gam_games')->insert(
			array(
				'name' => "Loup Garou de Thiercelieux",
				'description' => "",
				'status'=>1
			)
		);  
		
		DB::table('gam_games')->insert(
			array(
				'name' => "Test",
				'description' => "",
				'status'=>0
			)
		); 
		
		DB::table('gam_games')->insert(
			array(
				'name' => "Petits meurtres et faits divers",
				'description' => "",
				'status'=>0
			)
		); 
		
		DB::table('gam_games')->insert(
			array(
				'name' => "Pictionary",
				'description' => "",
				'status'=>1
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
        //		
		Schema::drop('gam_games'); 
    }
} 
