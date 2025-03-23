<?php

namespace App\Http\Controllers;

use App\Http\Actions\GooglePlay\HandleNotificationAction;
use App\Http\Actions\GooglePlay\VerifyPurchaseAction;
use App\Http\Actions\GooglePlay\VerifySubscriptionAction;
use Illuminate\Http\Request;
use App\Services\GooglePlayService;

class GooglePlayController extends Controller
{
    protected $googlePlayService;

    public function __construct(GooglePlayService $googlePlayService)
    {
        $this->googlePlayService = $googlePlayService;
    }

    public function testConnection()
    {
        $result = $this->googlePlayService->testConnection();
        return response()->json($result);
    }

    public function verifySubscription(Request $request)
    {
        $verifySubscription = new VerifySubscriptionAction($this->googlePlayService);
        return $verifySubscription->execute($request);
    }

    /**
     * Endpoint para verificar e registrar uma compra feita pelo usuário no app
     */
    public function verifyPurchase(Request $request)
    {
        $verifyPurchase = new VerifyPurchaseAction($this->googlePlayService);
        return $verifyPurchase->execute($request);
    }

    /**
     * Webhook para receber notificações da Google Play sobre alterações nas assinaturas
     */
    public function handleNotification(Request $request)
    {
        $notification = new HandleNotificationAction($this->googlePlayService);
        return $notification->execute($request);
    }
}
