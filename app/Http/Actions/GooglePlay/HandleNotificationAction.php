<?php

namespace App\Http\Actions\GooglePlay;

use App\Models\Subscription;
use App\Services\GooglePlayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HandleNotificationAction
{
    protected $googlePlayService;

    public function __construct(GooglePlayService $googlePlayService)
    {
        $this->googlePlayService = $googlePlayService;
    }

    public function execute(Request $request)
    {
        // Log da notificação recebida para depuração
        Log::info('Notificação Google Play recebida', [
            'data' => $request->all()
        ]);

        try {
            // Validar e extrair dados da notificação
            $notification = $request->all();

            // Verificar se é uma notificação válida
            if (!isset($notification['message']) || !isset($notification['message']['data'])) {
                return response()->json(['success' => false, 'message' => 'Dados da notificação inválidos'], 400);
            }

            // Decodificar os dados da notificação (geralmente é base64)
            $data = json_decode(base64_decode($notification['message']['data']), true);

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Dados da notificação não podem ser decodificados'], 400);
            }

            // Extrair informações relevantes
            $subscriptionNotification = $data['subscriptionNotification'] ?? null;

            if (!$subscriptionNotification) {
                return response()->json(['success' => false, 'message' => 'Notificação não contém dados de assinatura'], 400);
            }

            // Extrair detalhes da assinatura
            $purchaseToken = $subscriptionNotification['purchaseToken'] ?? null;
            $subscriptionId = $subscriptionNotification['subscriptionId'] ?? null;
            $notificationType = $subscriptionNotification['notificationType'] ?? null;

            if (!$purchaseToken || !$subscriptionId || !$notificationType) {
                return response()->json(['success' => false, 'message' => 'Informações essenciais ausentes na notificação'], 400);
            }

            // Verificar o estado atual da assinatura na Google Play
            $result = $this->googlePlayService->verifySubscription($purchaseToken, $subscriptionId);

            if (!$result['success']) {
                Log::error('Falha ao verificar assinatura após notificação', [
                    'purchase_token' => $purchaseToken,
                    'subscription_id' => $subscriptionId,
                    'error' => $result['message']
                ]);

                // Mesmo com erro, retornamos sucesso para a Google para evitar reenvios
                return response()->json(['success' => true]);
            }

            // Buscar a assinatura no banco de dados
            $subscription = Subscription::where('purchase_token', $purchaseToken)
                ->where('product_id', $subscriptionId)
                ->where('provider', 'google_play')
                ->first();

            // Se não encontrar a assinatura, pode ter sido uma compra fora do app ou outros cenários
            if (!$subscription) {
                Log::warning('Notificação recebida para assinatura não registrada', [
                    'purchase_token' => $purchaseToken,
                    'subscription_id' => $subscriptionId
                ]);

                // Mesmo assim, retornamos sucesso para a Google
                return response()->json(['success' => true]);
            }

            // Processar o status da assinatura
            $userId = $subscription->user_id;
            $purchaseData = $result['data'];
            $subscriptionStatus = $this->googlePlayService->processSubscriptionStatus($purchaseData, $subscriptionId, $userId);

            // Atualizar a assinatura no banco de dados
            $this->googlePlayService->updateSubscription($subscription, $purchaseData, $subscriptionStatus);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error(['HandleNotification error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
