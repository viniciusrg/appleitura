<?php

namespace App\Http\Controllers;

use App\Http\Actions\Apple\StoreAppleTransactionAction;
use App\Services\JwtApiService;
use App\Services\JwtNotificationService;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    private function fetchPurchaseHistory($transactionId)
    {
        $token = $this->getNotificationToken();

        $response = Http::withToken($token)->get("https://api.storekit-sandbox.itunes.apple.com/inApps/v2/history/{$transactionId}");

        if ($response->successful()) {
            return $response->json();
        }

        // dd($response);
        dump($response);
        throw new \Exception("Erro ao acessar a API da Apple: " . $response);
    }

    public function handleNotification(Request $request)
    {

        self::fetchPurchaseHistory('2000000836397208');
        dd('Fetched');

        $data = $request->all();
        $header = $request->headers;
        // $kid = $header->kid ?? null;

        $parts = explode('.', $data['signedPayload']);

        $header = json_decode(base64_decode($parts[0]), true);
        $payload = json_decode(base64_decode($parts[1]), true);
        dump($header);
        dd($payload);

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
        $publicKey = "-----BEGIN CERTIFICATE-----
MIIEMDCCA7agAwIBAgIQfTlfd0fNvFWvzC1YIANsXjAKBggqhkjOPQQDAzB1MUQw
QgYDVQQDDDtBcHBsZSBXb3JsZHdpZGUgRGV2ZWxvcGVyIFJlbGF0aW9ucyBDZXJ0
aWZpY2F0aW9uIEF1dGhvcml0eTELMAkGA1UECwwCRzYxEzARBgNVBAoMCkFwcGxl
IEluYy4xCzAJBgNVBAYTAlVTMB4XDTIzMDkxMjE5NTE1M1oXDTI1MTAxMTE5NTE1
MlowgZIxQDA+BgNVBAMMN1Byb2QgRUNDIE1hYyBBcHAgU3RvcmUgYW5kIGlUdW5l
cyBTdG9yZSBSZWNlaXB0IFNpZ25pbmcxLDAqBgNVBAsMI0FwcGxlIFdvcmxkd2lk
ZSBEZXZlbG9wZXIgUmVsYXRpb25zMRMwEQYDVQQKDApBcHBsZSBJbmMuMQswCQYD
VQQGEwJVUzBZMBMGByqGSM49AgEGCCqGSM49AwEHA0IABEFEYe/JqTqyQv/dtXka
uDHCScV129FYRV/0xiB24nCQkzQf3asHJONR5r0RA0aLvJ432hy1SZMouvyfpm26
jXSjggIIMIICBDAMBgNVHRMBAf8EAjAAMB8GA1UdIwQYMBaAFD8vlCNR01DJmig9
7bB85c+lkGKZMHAGCCsGAQUFBwEBBGQwYjAtBggrBgEFBQcwAoYhaHR0cDovL2Nl
cnRzLmFwcGxlLmNvbS93d2RyZzYuZGVyMDEGCCsGAQUFBzABhiVodHRwOi8vb2Nz
cC5hcHBsZS5jb20vb2NzcDAzLXd3ZHJnNjAyMIIBHgYDVR0gBIIBFTCCAREwggEN
BgoqhkiG92NkBQYBMIH+MIHDBggrBgEFBQcCAjCBtgyBs1JlbGlhbmNlIG9uIHRo
aXMgY2VydGlmaWNhdGUgYnkgYW55IHBhcnR5IGFzc3VtZXMgYWNjZXB0YW5jZSBv
ZiB0aGUgdGhlbiBhcHBsaWNhYmxlIHN0YW5kYXJkIHRlcm1zIGFuZCBjb25kaXRp
b25zIG9mIHVzZSwgY2VydGlmaWNhdGUgcG9saWN5IGFuZCBjZXJ0aWZpY2F0aW9u
IHByYWN0aWNlIHN0YXRlbWVudHMuMDYGCCsGAQUFBwIBFipodHRwOi8vd3d3LmFw
cGxlLmNvbS9jZXJ0aWZpY2F0ZWF1dGhvcml0eS8wHQYDVR0OBBYEFAMs8Pjs6VhW
GQlzE2ZOE+GX4Oo/MA4GA1UdDwEB/wQEAwIHgDAQBgoqhkiG92NkBgsBBAIFADAK
BggqhkjOPQQDAwNoADBlAjEA8yRNdskp506DFdPLghLLJwAv5J8hBGLaI8DExdcP
X+aBKjjO8eUo9KpfpcNYUY5YAjAPXmMXEZL+Q02adrmmshNxz3NnKm+ouQwU7vBT
n0LvlM7vps2YslVTamRYL4aSs5k=
-----END CERTIFICATE-----";

        $decoded = JWT::decode($response, new key($publicKey, 'HS256'));
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

    public function transactionStore(Request $request)
    {
        $trasaction = new StoreAppleTransactionAction();
        return $trasaction->execute($request);
    }
}
