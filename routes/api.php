<?php

use App\Http\Controllers\Api\V1\Bank\BankController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\LaravelBackendController;
use App\Http\Controllers\Api\V1\PromotionController;
use Illuminate\Support\Facades\Route;

// Local CMS content remains in AsiaSlot.
Route::get('promotion', [PromotionController::class, 'index']);
Route::get('banner', [BannerController::class, 'index']);

// Player identity, catalog, launch, balance and transaction authority now come
// exclusively from laravelBackend. Protected operations forward the caller's
// Bearer JWT; AsiaSlot no longer authenticates them against its local Sanctum DB.
Route::post('login', [LaravelBackendController::class, 'login']);
Route::post('logout', [LaravelBackendController::class, 'logout']);
Route::get('user', [LaravelBackendController::class, 'user']);
Route::post('profile', [LaravelBackendController::class, 'user']);

Route::get('providers', [LaravelBackendController::class, 'providers']);
Route::get('gameTypes', [LaravelBackendController::class, 'categories']);
Route::get('gameTypeProviders/{id}', [LaravelBackendController::class, 'providers']);
Route::get('gamelist', [LaravelBackendController::class, 'games']);
Route::get('hotgame', [LaravelBackendController::class, 'games']);
Route::get('gamedetail/{provider}/game_type/{category}', [LaravelBackendController::class, 'gameDetail']);
Route::get('game/{name}', [LaravelBackendController::class, 'game']);
Route::get('launchGame/{name}', [LaravelBackendController::class, 'launch'])->name('user.launchGame');

Route::get('wallet/currentWallet', [LaravelBackendController::class, 'balance']);
Route::get('game-log', [LaravelBackendController::class, 'transactions']);
Route::get('transaction/player-transactionlog', [LaravelBackendController::class, 'transactions']);
Route::post('transaction/deposit', [LaravelBackendController::class, 'deposit']);
Route::post('transaction/withdraw', [LaravelBackendController::class, 'withdraw']);

// Bank display data is AsiaSlot CMS data; it does not mutate player balance.
Route::get('bank/all', [BankController::class, 'all']);
