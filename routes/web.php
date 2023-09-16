<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

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


Route::middleware('auth')->group(function () {
    Route::get('cards/checkdouble', 'CardController@checkdouble');
    Route::get('profile', 'UserController@profile');

    /*Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    */
});

require __DIR__.'/auth.php';
