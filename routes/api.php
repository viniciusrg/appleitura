<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Autheticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/user')->group(function () {

        // User routes
        Route::get('/', [UserController::class, 'show']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::patch('/updateAccount', [AuthController::class, 'updateAccount']);

        // Questions routes
        Route::post('/StoreAnswer', [QuestionsController::class, 'store']);

        Route::middleware('admin')->group(function () {
            Route::get('/ShowAnswer/{user_id}', [QuestionsController::class, 'show']);
        });
    });
});
