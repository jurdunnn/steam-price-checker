<?php

use App\Http\Controllers\SteamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(SteamController::class)->group(function () {
    Route::get('/steam/get/{id}', 'get')->name('steam.get');
    Route::get('/steam/search/{query}', 'search')->name('steam.search');
});
