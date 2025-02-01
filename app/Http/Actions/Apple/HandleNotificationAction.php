<?php

namespace App\Http\Actions\Apple;

use App\Models\AppleTransaction;
use App\Models\Category;
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

            $category = Category::where('productId', $signedTransactionInfo[1]->productId)
                ->orderBy('created_at', 'desc')
                ->firstOrFail();
            $transaction = AppleTransaction::where('transactionId', $signedTransactionInfo[1]->transactionId)->first();
            $user = User::where('id', $transaction->user_id)->first();

            switch ($notificationType) {
                case ('SUBSCRIBED' || 'PURCHASE' || 'DID_RENEW'):
                    $this->processSubscribed($signedTransactionInfo, $signedRenewalInfo, $category, $user);
                    break;
                case ('EXPIRED' || 'DID_FAIL_TO_RENEW' || 'REFUND'):
                    $this->processCancel($signedTransactionInfo, $signedRenewalInfo, $category, $user);
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

    private function processSubscribed(array $signedTransactionInfo, array $signedRenewalInfo, Category $category, User $user): void
    {
        Log::info('Assinatura comprada', [$signedTransactionInfo, $signedRenewalInfo]);

        $isEmpty = $user->categories()->where('category_id', $category->id)->get()->isEmpty();
        if ($isEmpty) {
            $user->categories()->attach($category->id);
        }
    }

    private function processCancel(array $signedTransactionInfo, array $signedRenewalInfo, Category $category, User $user): void
    {
        Log::info('Assinatura cancelada', [$signedTransactionInfo, $signedRenewalInfo]);

        $user->categories()->detach($category->id);
        $user->tokens()->delete();
    }
}
