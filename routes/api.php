<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas autenticadas
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/user')->group(function () {

        Route::get('/', [UserController::class, 'show']);
        Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    });
});
