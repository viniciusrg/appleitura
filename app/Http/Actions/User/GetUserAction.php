<?php

namespace App\Http\Actions\User;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Log;

class GetUserAction
{
    public function execute($request)
    {
        try {
            return new UserResource($request->user());
        } catch (\Exception $e) {
            Log::error(['Show user error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
