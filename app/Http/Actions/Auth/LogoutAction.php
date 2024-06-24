<?php

namespace App\Http\Actions\Auth;

use Illuminate\Support\Facades\Log;

class LogoutAction
{
    public function execute($request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message:' => 'Successfully logged out.'], 200);
        } catch (\Exception $e) {
            Log::error(['User logout error: '] . $e);
            return response()->json(['message:' => $e->getMessage()]);
        }
    }
}
