<?php

namespace App\Http\Actions\PushNotification;

use App\Models\PushNotification;
use Illuminate\Support\Facades\Log;

class PushNotificationAction
{
    public function execute($request)
    {
        try {
            $pushNotification = PushNotification::create([
                'user_id' => $request->user()->id,
                'token' => $request->input('token'),
            ]);

            return $pushNotification ? response('', 200) : response()->json(['message' => 'PushNotification error.'], 400);
        } catch (\Exception $e) {
            Log::error(['Store push notification error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
