<?php

namespace App\Http\Controllers;

use App\Services\JwtService;
use AppleClient;
use AppleService_AppStore;
use Illuminate\Http\Request;

class AppStoreController extends Controller
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function getJwt()
    {
        $token = $this->jwtService->generateJwt();
        return $token;

        $client = new AppleClient();
        $client->setApiKey('/Users/viniciusgoulart/Documents/Jobs/Luiz/appleitura/storage/app/keys/SubscriptionKey_859ZN8XCR8.p8');
        $client->setIssuerId(config('appstore.issuer_id'));
        $client->setKeyIdentifier(config('appstore.key_id'));
        
        $appstore = new AppleService_AppStore($client);
        dd($appstore->apps->listApps());
        $ret = $appstore->apps->listApps([
          'limit' => 10
        ]);

        // return response()->json(['Response: ', $ret], 200);
        return $ret;
    }

    public function handleNotification (Request $request){
// Captura a notificação enviada pela Apple
$data = $request->all();

// Você pode verificar a assinatura da notificação para garantir segurança
// e depois processar os dados conforme necessário.
\Log::info('Recebido Apple Notification', $data);

// Processar a notificação conforme o tipo
$notificationType = $data['notificationType'] ?? null;
$payload = $data['data'] ?? [];

switch ($notificationType) {
    case 'DID_RENEW':
        // Renovação de assinatura
        $this->processRenewal($payload);
        break;

    case 'CANCEL':
        // Cancelamento de assinatura
        $this->processCancellation($payload);
        break;

    // Adicione outros tipos de notificações aqui
    default:
        \Log::warning('Tipo de notificação desconhecido', $data);
}

// Sempre retorne 200 OK
return response()->json(['status' => 'success'], 200);
}

private function processRenewal($payload)
{
// Lógica para tratar a renovação
}

private function processCancellation($payload)
{
// Lógica para tratar o cancelamento
}
    }
}
