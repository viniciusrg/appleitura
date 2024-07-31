<?php

namespace App\Http\Actions\Auth;

use App\Models\PasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class ResetPasswordAction
{
    public function execute($request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message: ' => 'User not found.'], 404);
            }

            $resetToken = PasswordResetToken::where('email', $request->email)->first();

            if ($resetToken->token != $request->token) {
                return response()->json(['message' => 'Invalid token.'], 404);
            }

            if (Carbon::now()->diffInMinutes($resetToken->created_at) > 15) {
                return response()->json(['message' => 'Token has expired.'], 404);
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            PersonalAccessToken::where('tokenable_id', $user->id)
                ->where('tokenable_type', get_class($user))
                ->delete();

            $token = $user->createToken('authToken')->plainTextToken;
            $expiresAt = Carbon::now()->addMinutes(config('sanctum.expiration'))->toDateTimeString();

            return response()->json([
                'message' => 'User reset password successfully',
                'token' => $token,
                'expires_at' => $expiresAt
            ], 201);
        } catch (\Exception $e) {
            Log::error(['User reset password error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
