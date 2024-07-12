<?php

namespace App\Http\Actions\Auth;

use Illuminate\Support\Facades\Log;

class LogoutAction
{
    public function execute($request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response(null, 204);
        } catch (\Exception $e) {
            Log::error(['User logout error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
