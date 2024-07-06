<?php

namespace App\Http\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UpdateAccountAction
{
    public function execute($request)
    {
        try {
            $data = ['password' => Hash::make($request->password)];

            $user = User::find($request->user()->id);
            $user->update($data);

            return response()->json(['message' => 'User updated account successfully', 'user' => $user], 200);
        } catch (\Exception $e) {
            Log::error(['User update account error: '] . $e);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
