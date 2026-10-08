<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
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

require __DIR__.'/auth.php';

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
Route::middleware('auth')->group(function () {
    Route::get('cards/checkdouble', 'CardController@checkdouble');
    Route::get('profile', 'UserController@profile')->name('profile');

    /*Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    */
});

//On filtre par permission
Route::group(['middleware' => ['auth','permission:user-edit']], function () {
    Route::resource('users', 'UserController')->names(['index'=>'users']);
});

Route::group(['middleware' => ['auth','permission:card-edit']], function () {
    Route::resource('cards', 'CardController')->names(['index'=>'cards']);
});

Route::group(['middleware' => ['auth','permission:game-edit']], function () {
    Route::resource('games', 'GameController')->names(['index'=>'games']);
});


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('sign-up', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('sign-up', [RegisterController::class, 'store'])->middleware('guest');
Route::get('sign-in', [SessionsController::class, 'create'])->middleware('guest')->name('login');
Route::post('sign-in', [SessionsController::class, 'store'])->middleware('guest');
Route::post('verify', [SessionsController::class, 'show'])->middleware('guest');
Route::post('reset-password', [SessionsController::class, 'update'])->middleware('guest')->name('password.update');
Route::get('verify', function () {
    return view('sessions.password.verify');
})->middleware('guest')->name('verify');
Route::get('/reset-password/{token}', function ($token) {
    return view('sessions.password.reset', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('sign-out', [SessionsController::class, 'destroy'])->middleware('auth')->name('logout');

