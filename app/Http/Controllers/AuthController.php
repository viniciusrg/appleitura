<?php

namespace App\Http\Controllers;

use App\Http\Actions\Auth\LoginAction;
use App\Http\Actions\Auth\LogoutAction;
use App\Http\Requests\AuthUserRequest;
use App\Http\Requests\StoreUpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(StoreUpdateUserRequest $request)
    {
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    public function login(AuthUserRequest $request)
    {
        $data = new LoginAction();
        return $data->execute($request);
    }

    public function logout(Request $request)
    {
        $data = new LogoutAction();
        return $data->execute($request);
    }
}
