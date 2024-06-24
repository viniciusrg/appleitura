<?php

namespace App\Http\Controllers;

use App\Http\Actions\User\GetUserAction;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show (Request $request) {
        $user = new GetUserAction();
        return $user->execute($request);
    }
}
