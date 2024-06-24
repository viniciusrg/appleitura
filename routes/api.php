<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rota autenticada
Route::middleware('auth:sanctum')->get('/authenticated-user', function (Request $request) {
    return response()->json(['user' => $request->user()]);
});