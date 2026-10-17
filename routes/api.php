<?php

use App\Http\Controllers\Api\DeployController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CardController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (application mobile)
|--------------------------------------------------------------------------
|
| Prefixe /api, reponses en JSON. Les routes protegees attendent l'en-tete
| "Authorization: Bearer <token>" (jeton obtenu par /v1/auth/login ou /v1/auth/register).
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    // Catalogue en acces libre (jeu hors connexion)
    Route::get('config', [CatalogController::class, 'config'])->name('config');
    Route::get('games', [CatalogController::class, 'games'])->name('games');
    Route::get('cards', [CatalogController::class, 'cards'])->name('cards');
    Route::get('cards/version', [CatalogController::class, 'cardsVersion'])->name('cards.version');
    Route::get('werewolf/cards', [CatalogController::class, 'werewolfCards'])->name('werewolf.cards');
    Route::post('werewolf/deck', [CatalogController::class, 'werewolfDeck'])->name('werewolf.deck');

    // Authentification
    Route::middleware('throttle:6,1')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password');
    });

    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('me', [AuthController::class, 'me'])->name('me');
        Route::put('me', [ProfileController::class, 'update'])->name('me.update');
        Route::delete('me', [AuthController::class, 'destroy'])->middleware('throttle:6,1')->name('me.destroy');

        // Cartes proposees par l'utilisateur
        Route::get('my/cards/check-double', [CardController::class, 'checkDouble'])->name('my.cards.check-double');
        Route::apiResource('my/cards', CardController::class)->names('my.cards');
    });
});

// Fin de deploiement (CI) : migrations + caches
Route::post('deploy', DeployController::class)->middleware('throttle:6,1')->name('api.deploy');
