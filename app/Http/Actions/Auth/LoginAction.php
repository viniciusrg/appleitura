<?php

namespace App\Http\Actions\Auth;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    public function execute($request)
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user = $request->user();
            $token = $user->createToken('authToken')->plainTextToken;
            $expiresAt = Carbon::now()->addMinutes(config('sanctum.expiration'))->toDateTimeString();

            return response()->json([
                'message' => 'User logged in.',
                'token' => $token,
                'expires_at' => $expiresAt
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('User login error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to login.'], 500);
        }
    }
}
