<?php

namespace App\Http\Controllers;

use App\Http\Actions\Auth\ForgotPasswordAction;
use App\Http\Actions\Auth\LoginAction;
use App\Http\Actions\Auth\LogoutAction;
use App\Http\Actions\Auth\RegisterAction;
use App\Http\Actions\Auth\ResetPasswordAction;
use App\Http\Requests\AuthUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\StoreUpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(StoreUpdateUserRequest $request)
    {
        dd('cu');
        try {
            dump("Entrei no register");
            $data = new RegisterAction();
            return $data->execute($request);
        } catch (\Exception $e) {
            Log::error(['User register error: ' . $e]);
        }
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

    public function forgotPassword(Request $request)
    {
        $forgot = new ForgotPasswordAction();
        return $forgot->execute($request);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $resetPassword = new ResetPasswordAction();
        return $resetPassword->execute($request);
    }
}
