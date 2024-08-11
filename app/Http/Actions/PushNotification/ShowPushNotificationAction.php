<?php

namespace App\Http\Actions\PushNotification;

use Illuminate\Support\Facades\Log;

class ShowPushNotificationAction
{
    public function execute($request)
    {
        try {
            return $request->user()->pushNotification()->get()->pluck('token');
        } catch (\Exception $e) {
            Log::error(['Index push notification error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
