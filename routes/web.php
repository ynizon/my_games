<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();
Route::get('/', 'HomeController@index');
Route::get('/info', 'HomeController@info');
Route::get('/cards/getall', 'CardController@getall');
Route::get('timesup/settings', 'TimesupController@settings');
Route::get('timesup', 'TimesupController@index');
Route::get('brainstorm/settings', 'BrainstormController@settings');
Route::get('brainstorm', 'BrainstormController@index');
Route::get('pictionary/settings', 'PictionaryController@settings');
Route::get('pictionary', 'PictionaryController@index');
Route::get('loupgaroudethiercelieux/settings', 'LoupGarouController@settings');
Route::get('loupgaroudethiercelieux', 'LoupGarouController@index');
Route::get('taboo/settings', 'TabooController@settings');
Route::get('taboo', 'TabooController@index');

//On filtre par permission
Route::group(['middleware' => ['auth','permission:user-edit']], function () {	
	Route::resource('users', 'UserController');
});

Route::group(['middleware' => ['auth','permission:card-edit']], function () {	
	Route::resource('cards', 'CardController');	
});

Route::group(['middleware' => ['auth','permission:game-edit']], function () {	
	Route::resource('games', 'GameController');
});



//Pour tous les connectes
Route::group(['middleware' => ['auth']], function () {	
	try{		
		Route::get('cron', 'CronController@index');
		Route::get('cards/checkdouble', 'CardController@checkdouble');		
		Route::get('profile', 'UserController@profile');
	}catch(Exception $e){
		header("location: /");
	}
});
