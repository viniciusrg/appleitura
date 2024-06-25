<?php

namespace App\Http\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class DeleteAccountAction
{
    public function execute($request)
    {
        try {
            $user = User::find($request->user()->id);
            $user->delete();

            return response()->json(['message' => 'User delete account successfully'], 200);
        } catch (\Exception $e) {
            Log::error(['User delete account error: '] . $e);
            return response()->json(['message:' => $e->getMessage()], 500);
        }
    }
}
