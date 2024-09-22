<?php

namespace App\Http\Actions\User;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeleteUserAccountAction
{
    public function execute($request)
    {
        try {
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json(['Invalid user or password.'])->setStatusCode(Response::HTTP_UNAUTHORIZED);
            };
    
            $user = Auth::user();
            $user = User::find($user->id);
    
            return response($user->delete())->setStatusCode(Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            Log::error(['User delete account error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
