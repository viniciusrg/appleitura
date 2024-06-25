<?php

namespace App\Http\Controllers;

use App\Http\Actions\User\DeleteAccountAction;
use App\Http\Actions\User\GetUserAction;
use App\Http\Actions\User\UpdateAccountAction;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show (Request $request) {
        $user = new GetUserAction();
        return $user->execute($request);
    }

    public function updateAccount(Request $request)
    {
        $data = new UpdateAccountAction();
        return $data->execute($request);
    }

    public function deleteAccount(Request $request)
    {
        $data = new DeleteAccountAction();
        return $data->execute($request);
    }
}
