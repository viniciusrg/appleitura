<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
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
        Route::patch('/updateAccount', [UserController::class, 'updateAccount']);
        Route::delete('/deleteAccount', [UserController::class, 'deleteAccount']);

        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);

        // Questions routes
        Route::post('/StoreAnswer', [QuestionsController::class, 'store']);

        // Book routes
        Route::get('/books', [BookController::class, 'index']);
        Route::get('/book/{book_id}', [BookController::class, 'show']);

        // Favorite routes
        Route::post('/favorite/book/{book_id}', [FavoriteController::class, 'store']);
        Route::get('/favorite/book', [FavoriteController::class, 'index']);
    });

    // Admin routes
    Route::prefix('/admin')->group(function () {

        Route::middleware('admin')->group(function () {
            // Show user
            Route::get('/answer/{user_id}', [QuestionsController::class, 'show']);

            // Admin book routes
            Route::post('/book', [BookController::class, 'store']);
            Route::patch('/book', [BookController::class, 'update']);
        });
    });
});
