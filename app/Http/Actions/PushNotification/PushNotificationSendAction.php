<?php

namespace App\Http\Actions\PushNotification;

use App\Models\NotificationMessage;
use App\Models\PushNotification;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PushNotificationSendAction
{
    public function execute()
    {
        try {
            $tokens = PushNotification::inRandomOrder()->take(599)->pluck('token')->toArray();
            $notificationData = NotificationMessage::inRandomOrder()->select('message', 'title')->first();

            // Instancia o cliente HTTP
            $client = new Client();

            // Envia notificação para cada token
            foreach ($tokens as $token) {
                $response = $client->post('https://exp.host/--/api/v2/push/send', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'to' => $token,
                        'sound' => 'default',
                        'title' => $notificationData->title,
                        'body' => $notificationData->message,
                    ],
                ]);

                if ($response->getStatusCode() != 200) {
                    Log::error('Erro ao enviar a notificação para o token ' . $token . ': ' . $response->getBody());
                }
            }

            return response()->json(['message' => 'Notificações enviadas com sucesso.'], 200);
        } catch (\Exception $e) {
            Log::error(['Store push notification error: ' . $e]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
