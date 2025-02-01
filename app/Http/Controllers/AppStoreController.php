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

        // self::fetchPurchaseHistory('2000000836397208');
        // dump('Fetched');

        $data = $request->all();
        $header = $request->headers;
        $signedTransactionInfo = [];
        Log::info('Headers', [$header]);
        Log::info('Recebido Apple Notification', $data);

        $parts = explode('.', $data['signedPayload']);

        // dd($data);

        $header = json_decode(base64_decode($parts[0]), true);
        $payload = json_decode(base64_decode($parts[1]), true);

        // dump("header: ");
        // dump($header);

        // dump("payload: ");
        // dump($payload);

        if ($payload['data']['signedTransactionInfo']) {
            $payloadParts = explode('.', $payload['data']['signedTransactionInfo']);

            foreach ($payloadParts as $index => $payloadPart) {
                if ($index < 2) {
                    $signedTransactionInfo[] = json_decode(base64_decode($payloadPart, true));
                }
            }
        };

        dump("signedTransactionInfo: ");
        dump($signedTransactionInfo);

        if ($payload['data']['signedRenewalInfo']) {
            $payloadParts = explode('.', $payload['data']['signedRenewalInfo']);

            foreach ($payloadParts as $index => $payloadPart) {
                if ($index < 2) {
                    $signedRenewalInfo[] = json_decode(base64_decode($payloadPart, true));
                }
            }
        };

        dump("signedRenewalInfo: ");
        dump($signedRenewalInfo);


        // Processar a notificação conforme o tipo
        $notificationType = $signedTransactionInfo[1]->transactionReason ?? null;
        $payload = $data['data'] ?? [];

        // dump("data:");
        // dump($data);

        dump("NotificationType");
        dump($notificationType);

        switch ($notificationType) {
            case ('SUBSCRIBED' || 'PURCHASE'):
                $this->processSubscribed($signedTransactionInfo, $signedRenewalInfo);
                break;

            case 'DID_RENEW':
                $this->processRenewal($signedTransactionInfo, $signedRenewalInfo);
                break;

            case ('EXPIRED' || 'DID_FAIL_TO_RENEW' || 'REFUND'):
                $this->processCancel($signedTransactionInfo, $signedRenewalInfo);
                break;

            default:
                Log::warning('Tipo de notificação desconhecido', $data);
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function transactionStore(Request $request)
    {
        $trasaction = new StoreAppleTransactionAction();
        return $trasaction->execute($request);
    }

    private function processSubscribed(array $signedTransactionInfo, array $signedRenewalInfo)
    {
        Log::info('Assinatura comprada', [$signedTransactionInfo, $signedRenewalInfo]);
    }

    private function processRenewal(array $signedTransactionInfo, array $signedRenewalInfo)
    {

    }

    private function processCancel(array $signedTransactionInfo, array $signedRenewalInfo)
    {
        Log::info('Assinatura cancelada', [$signedTransactionInfo, $signedRenewalInfo]);
    }

}
