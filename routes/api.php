<?php

use App\Http\Controllers\BarbershopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/barbershops', [BarbershopController::class, 'getBarbershops']);
Route::get('/getslots', [BarbershopController::class, 'getAvailableSlots']);
Route::post('/book', [BarbershopController::class, 'bookSlot']);
