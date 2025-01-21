<?php

namespace App\Http\Controllers;

use App\Services\JwtService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    }

    public function handleNotification(Request $request)
    {

        $data = $request->all();

        $header = $request->headers;
        // $kid = $header->kid ?? null;

        // dd($header);
        // dd($data['signedPayload']);
        $result = self::decodToken($data['signedPayload']);
        // dd($result);

        Log::info('Headers', $header);
        Log::info('Recebido Apple Notification', $data);

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
                // Log::warning('Tipo de notificação desconhecido', $data);
        }

        // Sempre retorne 200 OK
        return response()->json(['status' => 'success'], 200);
    }

    private function decodToken($response)
    {
        $decoded = JWT::decode($response, new key('G5AFUS4VS9', 'HS256'));
        $decodedArray = (array) $decoded;
        return $decodedArray;
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
