<?php

namespace App\Http\Actions\Apple;

use App\Models\AppleTransaction;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HandleNotificationAction
{
    public function execute(Request $request)
    {
        try {
            $data = $request->all();
            $header = $request->headers;
            $signedTransactionInfo = [];
            Log::info('Headers', [$header]);
            Log::info('Recebido Apple Notification', $data);

            $parts = explode('.', $data['signedPayload']);

            $header = json_decode(base64_decode($parts[0]), true);
            $payload = json_decode(base64_decode($parts[1]), true);

            if ($payload['data']['signedTransactionInfo']) {
                $payloadParts = explode('.', $payload['data']['signedTransactionInfo']);

                foreach ($payloadParts as $index => $payloadPart) {
                    if ($index < 2) {
                        $signedTransactionInfo[] = json_decode(base64_decode($payloadPart, true));
                    }
                }
            };

            if ($payload['data']['signedRenewalInfo']) {
                $payloadParts = explode('.', $payload['data']['signedRenewalInfo']);

                foreach ($payloadParts as $index => $payloadPart) {
                    if ($index < 2) {
                        $signedRenewalInfo[] = json_decode(base64_decode($payloadPart, true));
                    }
                }
            };

            $notificationType = $signedTransactionInfo[1]->transactionReason ?? null;

            $subcategory = Subcategory::where('productId', $signedTransactionInfo[1]->productId)
                ->orderBy('created_at', 'desc')
                ->firstOrFail();

            $transaction = AppleTransaction::where('transactionId', $signedTransactionInfo[1]->transactionId)->first();
            $user = User::where('id', $transaction->user_id)->first();

            switch ($notificationType) {
                case 'SUBSCRIBED':
                case 'PURCHASE':
                case 'DID_RENEW':
                    $this->processSubscribed($signedTransactionInfo, $signedRenewalInfo, $subcategory, $user);
                    break;

                case 'EXPIRED':
                case 'DID_FAIL_TO_RENEW':
                case 'REFUND':
                    $this->processCancel($signedTransactionInfo, $signedRenewalInfo, $subcategory, $user);
                    break;

                default:
                    Log::warning('Tipo de notificação desconhecido', $data);
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error(['HandleNotification error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function processSubscribed(array $signedTransactionInfo, array $signedRenewalInfo, Subcategory $subcategory, User $user): void
    {
        Log::info('Assinatura comprada', [$signedTransactionInfo, $signedRenewalInfo]);

        $isEmpty = $user->subcategories()->where('category_id', $subcategory->id)->get()->isEmpty();
        if ($isEmpty) {
            $user->subcategories()->attach($subcategory->id);
        }
    }

    private function processCancel(array $signedTransactionInfo, array $signedRenewalInfo, Subcategory $subcategory, User $user): void
    {
        Log::info('Assinatura cancelada', [$signedTransactionInfo, $signedRenewalInfo]);

        $user->subcategories()->detach($subcategory->id);
        // $user->tokens()->delete();
    }
}
