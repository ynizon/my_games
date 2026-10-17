<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlayController;
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

Route::get('/', 'HomeController@index');
Route::get('/info', 'HomeController@info');
Route::get('/cards/getall', 'CardController@getall');
Route::view('/confidentialite', 'legal.privacy')->name('privacy');
Route::view('/suppression-compte', 'legal.delete-account')->name('account.delete');

//Jeux
foreach (array_keys(PlayController::GAMES) as $game) {
    Route::get($game.'/settings', [PlayController::class, 'settings'])->defaults('game', $game);
    Route::get($game, [PlayController::class, 'index'])->defaults('game', $game);
}
foreach (array_keys(PlayController::OLD_SLUGS) as $old) {
    Route::get($old.'/settings', [PlayController::class, 'redirectOld'])->defaults('old', $old)->defaults('suffix', '/settings');
    Route::get($old, [PlayController::class, 'redirectOld'])->defaults('old', $old);
}

Route::middleware('auth')->group(function () {
    Route::get('cards/checkdouble', 'CardController@checkdouble');
    Route::get('profile', 'UserController@profile')->name('profile');
    Route::put('profile', 'UserController@updateProfile')->name('profile.update');
    Route::delete('profile', 'UserController@destroyProfile')->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('sign-out', [SessionsController::class, 'destroy'])->name('logout');
});

//On filtre par permission
Route::group(['middleware' => ['auth','permission:user-edit']], function () {
    Route::resource('users', 'UserController')->names(['index'=>'users']);
});

Route::group(['middleware' => ['auth','permission:card-edit|card-propose']], function () {
    Route::resource('cards', 'CardController')->names(['index'=>'cards']);
});

Route::group(['middleware' => ['auth','permission:game-edit']], function () {
    Route::resource('games', 'GameController')->names(['index'=>'games']);
});

//Authentification
Route::middleware('guest')->group(function () {
    Route::get('sign-up', [RegisterController::class, 'create'])->name('register');
    Route::post('sign-up', [RegisterController::class, 'store']);
    Route::get('sign-in', [SessionsController::class, 'create'])->name('login');
    Route::post('sign-in', [SessionsController::class, 'store']);
    Route::get('verify', function () {
        return view('sessions.password.verify');
    })->name('verify');
    Route::post('verify', [SessionsController::class, 'show']);
    Route::get('/reset-password/{token}', function ($token) {
        return view('sessions.password.reset', ['token' => $token]);
    })->name('password.reset');
    Route::post('reset-password', [SessionsController::class, 'update'])->name('password.update');
});
