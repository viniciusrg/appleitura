<?php

namespace App\Http\Actions\GooglePlay;

use App\Services\GooglePlayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerifyPurchaseAction
{
    protected $googlePlayService;

    public function __construct(GooglePlayService $googlePlayService)
    {
        $this->googlePlayService = $googlePlayService;
    }

    public function execute(Request $request)
    {
        Log::info("GooglePlay - APP", [
            'Request: ' => $request->all(),
            'UserId: ' => Auth::id(),
        ]);
        $validated = $request->validate([
            'subscription_id' => 'required|string',
            'purchase_token' => 'required|string',
        ]);

        try {
            // Verificar a assinatura na Google Play
            $result = $this->googlePlayService->verifySubscription(
                $validated['purchase_token'],
                $validated['subscription_id']
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível verificar a assinatura: ' . $result['message']
                ], 400);
            }

            $purchaseData = $result['data'];
            $subscriptionId = $validated['subscription_id'];

            // Processar o status da assinatura
            $subscriptionStatus = $this->googlePlayService->processSubscriptionStatus($purchaseData, $subscriptionId);

            // Registrar ou atualizar a assinatura no banco de dados
            $subscription = $this->googlePlayService->storeSubscription(
                Auth::id(),
                $validated['subscription_id'],
                $validated['purchase_token'],
                $purchaseData,
                $subscriptionStatus
            );

            return response()->json([
                'success' => true,
                'message' => 'Assinatura verificada e registrada com sucesso',
                'data' => [
                    'subscription' => $subscription,
                    'status' => $subscriptionStatus
                ]
            ]);
        } catch (\Exception $e) {
            Log::error(['VerifyPurchase error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
