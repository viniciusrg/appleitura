<?php

namespace App\Http\Controllers;

use App\Services\JwtService;
use GuzzleHttp\Client;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppStoreController extends Controller
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Retorna um JWT gerado para a App Store Server API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJwt()
    {
        $jwt = $this->jwtService->generateJwt();

        return response()->json([
            'token' => $jwt,
        ]);
    }

    public function handleStoreNotification(Request $request)
{
    $notificationData = $request->getContent();
    Log::error(['StoreNotification: ', $notificationData]);

    // Obtenha a chave pública da Apple
    $client = new Client();
    $response = $client->get('https://appleid.apple.com/auth/keys');
    $publicKey = json_decode($response->getBody()->getContents(), true)['keys'][0]['n'];

    // Converta a chave pública para o formato correto, se necessário (exemplo, em base64)

    // Valide o JWT (se necessário)
    $decoded = JWT::decode($notificationData, $publicKey, ['RS256']);

    // Processar os dados da notificação
    $transactionId = $decoded->data->signedTransactionInfo->transactionId;
    $originalTransactionId = $decoded->data->signedTransactionInfo->originalTransactionId;
    $status = $decoded->data->signedTransactionInfo->status;

    // Lógica para lidar com a notificação
    // Exemplo: atualizar o status da assinatura no banco de dados
}

}
