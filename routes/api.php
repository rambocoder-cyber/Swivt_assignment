<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\OperatorIntegrationController;
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

Route::post('normal-login', [AuthController::class,'normalLogin']);
Route::post('sign-out', [AuthController::class,'signOut']);

Route::controller(OperatorIntegrationController::class)->group(function () {
    Route::post('authenticate', 'authenticate');
    Route::post('balance', 'getBalance');
    Route::post('bet', 'placeBet');
    Route::post('settle-bet', 'settleBet');
});

Route::controller(GameController::class)->group(function(){
    Route::get('playGame', 'playGame');
    Route::post('/list-games', 'listGames');
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
