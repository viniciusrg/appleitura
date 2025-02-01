<?php

namespace App\Http\Controllers;

use App\Http\Actions\Apple\HandlenotificationAction;
use App\Http\Actions\Apple\StoreAppleTransactionAction;
use App\Services\JwtApiService;
use App\Services\JwtNotificationService;
use Illuminate\Http\Request;

class AppStoreController extends Controller
{
    protected JwtApiService $jwtService;

    protected JwtNotificationService $jwtNotificationService;

    public function __construct(JwtApiService $jwtService, JwtNotificationService $jwtNotificationService)
    {
        $this->jwtService = $jwtService;
        $this->jwtNotificationService = $jwtNotificationService;
    }

    public function getJwt()
    {
        $token = $this->jwtService->generateJwt();
        return $token;
    }

    public function getNotificationToken()
    {
        $token = $this->jwtNotificationService->generateJwt();
        return $token;
    }

    public function handleNotification(Request $request)
    {
        $handleNotification = new HandleNotificationAction();
        return $handleNotification->execute($request);
    }

    public function transactionStore(Request $request)
    {
        $trasaction = new StoreAppleTransactionAction();
        return $trasaction->execute($request);
    }

}
