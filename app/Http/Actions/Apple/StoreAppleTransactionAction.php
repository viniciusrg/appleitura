<?php

namespace App\Http\Actions\Apple;

use App\Models\AppleTransaction;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StoreAppleTransactionAction
{
    public function execute($request)
    {
        try {
            $data = $request->only(['transactionId', 'user_id', 'inAppOwnershipType', 'subscriptionGroupIdentifier', 'type']);
            
            if (empty($data)){
                return "Dados inválidos.";
            }

            AppleTransaction::create($data);
            return response('', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(['Transaction error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
