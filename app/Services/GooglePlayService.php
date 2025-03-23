<?php

namespace App\Services;

use App\Models\Subcategory;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Google\Client;
use Google\Service\AndroidPublisher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GooglePlayService
{
    protected $client;
    protected $service;
    protected $packageName;

    public function __construct()
    {
        $this->packageName = config('services.google_play.package_name');
        $this->initializeClient();
    }

    private function initializeClient()
    {
        try {
            // Criar o cliente do Google
            $this->client = new Client();
            
            // Configurar o escopo da API necessário para assinaturas
            $this->client->setScopes([AndroidPublisher::ANDROIDPUBLISHER]);
            
            // Carregar credenciais do arquivo JSON da conta de serviço
            $this->client->setAuthConfig(storage_path('app/googlekeys/livall-445019-ed426dd23106.json'));
            
            // Inicializar o serviço do Android Publisher
            $this->service = new AndroidPublisher($this->client);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao inicializar cliente Google Play: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Método para testar a conexão com a API do Google Play
     */
    public function testConnection()
    {
        try {
            // Tenta buscar detalhes de um produto de assinatura para verificar se a conexão funciona
            $subscriptionId = 'vida_positiva_mensal'; // Substitua pelo ID de uma assinatura real
            
            $subscription = $this->service->monetization_subscriptions->get(
                $this->packageName,
                $subscriptionId
            );
            
            return [
                'success' => true,
                'message' => 'Conexão com Google Play API realizada com sucesso',
                'data' => $subscription
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao testar conexão com Google Play: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Falha ao conectar com Google Play API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar uma compra de assinatura
     */
    public function verifySubscription($purchaseToken, $subscriptionId)
    {
        try {
            $purchase = $this->service->purchases_subscriptions->get(
                $this->packageName,
                $subscriptionId,
                $purchaseToken
            );
            
            return [
                'success' => true,
                'data' => $purchase
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao verificar assinatura: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Falha ao verificar assinatura: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Processa o status da assinatura a partir dos dados da compra
     */
    public function processSubscriptionStatus($purchaseData, $subscriptionId = null, $userId = null)
    {
        // Obter o estado da assinatura
        $paymentState = $purchaseData->getPaymentState() ?? null;
        $expiryTimeMillis = $purchaseData->getExpiryTimeMillis() ?? null;

        // Converter milissegundos para timestamp Unix (segundos)
        $expiryTime = $expiryTimeMillis ? (int)($expiryTimeMillis / 1000) : null;
        $now = time();

        // Verificar se a assinatura está ativa
        // $isActive = $paymentState == 1 && ($expiryTime > $now);
        $isActive = $expiryTime > $now;
        // $isActive = true;

        if (!$isActive && $subscriptionId && $userId){
            $user = User::find($userId);
            $subcategory = Subcategory::where('productId', $subscriptionId)
                ->orderBy('created_at', 'desc')
                ->firstOrFail();
            $user->subcategories()->detach($subcategory->id);
        }

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
    public function storeSubscription($userId, $subscriptionId, $purchaseToken, $purchaseData, $status)
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
        $subcategory = Subcategory::where('productId', $subscriptionId)
                ->orderBy('created_at', 'desc')
                ->firstOrFail();
        $isEmpty = $user->subcategories()->where('category_id', $subcategory->id)->get()->isEmpty();
        if ($isEmpty) {
            $user->subcategories()->attach($subcategory->id);
        }

        // $user = User::find($userId);

        // if ($user && $status['active']) {
        //     $user->has_active_subscription = true;
        //     $user->subscription_ends_at = $status['expires_at'];
        //     $user->save();
        // } else if ($user && !$status['active']) {
        //     // Verificar se o usuário tem outras assinaturas ativas
        //     $hasOtherActiveSubscriptions = Subscription::where('user_id', $userId)
        //         ->where('status', 'active')
        //         ->where('id', '!=', $subscription->id)
        //         ->exists();

        //     if (!$hasOtherActiveSubscriptions) {
        //         $user->has_active_subscription = false;
        //         $user->subscription_ends_at = null;
        //         $user->save();
        //     }
        // }

        return $subscription;
    }

    /**
     * Atualiza uma assinatura existente
     */
    public function updateSubscription($subscription, $purchaseData, $status)
    {
        $subscription->status = $status['active'] ? 'active' : 'expired';
        $subscription->expires_at = $status['expires_at'];
        $subscription->auto_renewing = $status['auto_renewing'];
        $subscription->payment_state = $status['payment_state'];
        $subscription->cancel_reason = $status['cancel_reason'];
        $subscription->provider_data = $status['raw_response'];
        $subscription->save();

        return $subscription;
    }
}