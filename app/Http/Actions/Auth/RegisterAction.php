<?php

namespace App\Http\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterAction
{
    public function execute($request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->permission()->create([
                'type' => 'common'
            ]);

            DB::commit();

            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            DB::roolBack();
            Log::error(['User register error: '] . $e);
            return response()->json(['message:' => $e->getMessage()], 500);
        }
    }
}
