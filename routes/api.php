<?php

use App\Http\Controllers\SearchController;
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

Route::controller(SearchController::class)->group(function () {
    Route::get('/steam/search/{query}', 'search')->name('steam.search');
    Route::get('/steam/search/get/{id}', 'get')->name('steam.get');
});
