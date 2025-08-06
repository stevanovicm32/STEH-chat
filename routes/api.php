<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SobaController;
use App\Http\Controllers\PorukaController;

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

// Javne rute za autentifikaciju
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Zaštićene rute (zahtevaju autentifikaciju)
Route::middleware('auth:sanctum')->group(function () {
    // Autentifikacija
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Rute za sobe
    Route::get('/sobe', [SobaController::class, 'index']);
    Route::post('/sobe', [SobaController::class, 'store']);
    Route::get('/sobe/{id}', [SobaController::class, 'show']);
    Route::put('/sobe/{id}', [SobaController::class, 'update']);
    Route::delete('/sobe/{id}', [SobaController::class, 'destroy']);
    
    // Dodatne rute za sobe
    Route::post('/sobe/{id}/pridruzi-se', [SobaController::class, 'pridruziSe']);
    Route::delete('/sobe/{id}/napusti', [SobaController::class, 'napusti']);

    // Rute za poruke
    Route::get('/sobe/{sobaId}/poruke', [PorukaController::class, 'index']);
    Route::post('/poruke', [PorukaController::class, 'store']);
    Route::get('/poruke/{id}', [PorukaController::class, 'show']);
    Route::put('/poruke/{id}', [PorukaController::class, 'update']);
    Route::delete('/poruke/{id}', [PorukaController::class, 'destroy']);
    
    // Dodatne rute za poruke
    Route::patch('/poruke/{id}/oznaci-procitana', [PorukaController::class, 'oznaciKaoProcitanu']);
    Route::get('/sobe/{sobaId}/poruke/pretrazi', [PorukaController::class, 'pretrazi']);
}); 