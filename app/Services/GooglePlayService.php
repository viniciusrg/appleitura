<?php

namespace App\Services;

use Google\Client;
use Google\Service\AndroidPublisher;
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
}