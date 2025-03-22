<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GooglePlayService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $validated = $request->validate([
            'purchase_token' => 'required|string',
            'subscription_id' => 'required|string',
        ]);

        $result = $this->googlePlayService->verifySubscription(
            $validated['purchase_token'],
            $validated['subscription_id']
        );

        return response()->json($result);
    }

    /**
     * Endpoint para verificar e registrar uma compra feita pelo usuário no app
     */
    public function verifyPurchase(Request $request)
    {
        Log::info("GooglePlay - APP", [
            'Request: ' => $request->all(),
            'UserId: ' => Auth::id(),
        ]);
        $validated = $request->validate([
            'subscription_id' => 'required|string',
            'purchase_token' => 'required|string',
            'user_id' => 'required|integer|exists:users,id'
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

            // Processar o status da assinatura
            $subscriptionStatus = $this->processSubscriptionStatus($purchaseData);

            // Registrar ou atualizar a assinatura no banco de dados
            $subscription = $this->storeSubscription(
                $validated['user_id'],
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
            Log::error('Erro ao verificar assinatura Google: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a verificação da assinatura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook para receber notificações da Google Play sobre alterações nas assinaturas
     */
    public function handleNotification(Request $request)
    {
        // Log da notificação recebida para depuração
        Log::info('Notificação Google Play recebida', [
            'data' => $request->all()
        ]);

        return;

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
            $purchaseData = $result['data'];
            $subscriptionStatus = $this->processSubscriptionStatus($purchaseData);

            // Atualizar a assinatura no banco de dados
            $this->updateSubscription($subscription, $purchaseData, $subscriptionStatus);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar notificação Google Play: ' . $e->getMessage());

            // Retornamos sucesso mesmo em caso de erro para evitar reenvios
            return response()->json(['success' => true]);
        }
    }

    /**
     * Processa o status da assinatura a partir dos dados da compra
     */
    private function processSubscriptionStatus($purchaseData)
    {
        // Obter o estado da assinatura
        $paymentState = $purchaseData->getPaymentState() ?? null;
        $expiryTimeMillis = $purchaseData->getExpiryTimeMillis() ?? null;

        // Converter milissegundos para timestamp Unix (segundos)
        $expiryTime = $expiryTimeMillis ? (int)($expiryTimeMillis / 1000) : null;
        $now = time();

        // Verificar se a assinatura está ativa
        $isActive = $paymentState == 1 && ($expiryTime > $now);

        // Obter outros estados relevantes
        $autoRenewing = $purchaseData->getAutoRenewing() ?? false;
        $cancelReason = $purchaseData->getCancelReason() ?? null;

        return [
            'active' => $isActive,
            'expires_at' => $expiryTime ? Carbon::createFromTimestamp($expiryTime) : null,
            'auto_renewing' => $autoRenewing,
            'payment_state' => $paymentState,
            'cancel_reason' => $cancelReason,
            'raw_response' => json_encode($purchaseData)
        ];
    }

    /**
     * Armazena ou atualiza a assinatura no banco de dados
     */
    private function storeSubscription($userId, $subscriptionId, $purchaseToken, $purchaseData, $status)
    {
        // Buscar assinatura existente ou criar nova
        $subscription = Subscription::updateOrCreate(
            [
                'provider' => 'google_play',
                'product_id' => $subscriptionId,
                'purchase_token' => $purchaseToken
            ],
            [
                'user_id' => $userId,
                'status' => $status['active'] ? 'active' : 'expired',
                'expires_at' => $status['expires_at'],
                'auto_renewing' => $status['auto_renewing'],
                'payment_state' => $status['payment_state'],
                'cancel_reason' => $status['cancel_reason'],
                'provider_data' => $status['raw_response']
            ]
        );

        // Atualizar status de assinatura do usuário
        $user = User::find($userId);

        if ($user && $status['active']) {
            $user->has_active_subscription = true;
            $user->subscription_ends_at = $status['expires_at'];
            $user->save();
        } else if ($user && !$status['active']) {
            // Verificar se o usuário tem outras assinaturas ativas
            $hasOtherActiveSubscriptions = Subscription::where('user_id', $userId)
                ->where('status', 'active')
                ->where('id', '!=', $subscription->id)
                ->exists();

            if (!$hasOtherActiveSubscriptions) {
                $user->has_active_subscription = false;
                $user->subscription_ends_at = null;
                $user->save();
            }
        }

        return $subscription;
    }

    /**
     * Atualiza uma assinatura existente
     */
    private function updateSubscription($subscription, $purchaseData, $status)
    {
        $subscription->status = $status['active'] ? 'active' : 'expired';
        $subscription->expires_at = $status['expires_at'];
        $subscription->auto_renewing = $status['auto_renewing'];
        $subscription->payment_state = $status['payment_state'];
        $subscription->cancel_reason = $status['cancel_reason'];
        $subscription->provider_data = $status['raw_response'];
        $subscription->save();

        // Atualizar status de assinatura do usuário
        $user = User::find($subscription->user_id);

        if ($user) {
            if ($status['active']) {
                $user->has_active_subscription = true;
                $user->subscription_ends_at = $status['expires_at'];
            } else {
                // Verificar se o usuário tem outras assinaturas ativas
                $hasOtherActiveSubscriptions = Subscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('id', '!=', $subscription->id)
                    ->exists();

                if (!$hasOtherActiveSubscriptions) {
                    $user->has_active_subscription = false;
                    $user->subscription_ends_at = null;
                }
            }

            $user->save();
        }

        return $subscription;
    }
}
