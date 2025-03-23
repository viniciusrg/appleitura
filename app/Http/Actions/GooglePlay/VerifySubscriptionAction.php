<?php

namespace App\Http\Actions\GooglePlay;

use App\Services\GooglePlayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifySubscriptionAction
{
    protected $googlePlayService;

    public function __construct(GooglePlayService $googlePlayService)
    {
        $this->googlePlayService = $googlePlayService;
    }

    public function execute(Request $request)
    {
        try {
            $validated = $request->validate([
                'purchase_token' => 'required|string',
                'subscription_id' => 'required|string',
            ]);

            $result = $this->googlePlayService->verifySubscription(
                $validated['purchase_token'],
                $validated['subscription_id']
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error(['VerifySubscription error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
