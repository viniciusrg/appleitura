<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::post('/', function (Request $request){
    // $user = User::create(['email' => 'vini1@gmail.com', 'password' => '123456', 'name' => 'vini']);
    // Auth::login($user, true);

    Auth::attempt(['email' => 'vini1@gmail.com', 'password' => '123456'], true);
    $request->session()->regenerate();
    return Auth::check();
});
Route::get('/', function (){
    dd(Auth::id());
});