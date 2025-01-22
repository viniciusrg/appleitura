<?php

namespace App\Services;

use Firebase\JWT\JWT;

class JwtNotificationService
{
    protected string $keyId;
    protected string $issuerId;
    protected string $privateKey;

    public function __construct()
    {
        $this->keyId = config('appstore.key_id'); // ID da chave
        $this->issuerId = config('appstore.issuer_id'); // ID do time
        $this->privateKey = file_get_contents(storage_path('app/keys/AuthKey_' . $this->keyId . '.p8'));
    }

    public function generateJwt(): string
    {
        $payload = [
            "iss" => $this->issuerId,
            "exp" => time() + 1199,
            "aud" => "appstoreconnect-v1",
        ];

        $header = [
            "alg" => "ES256",
            "kid" => $this->keyId,
            "typ" => "JWT",
        ];

        $token = JWT::encode($payload, $this->privateKey, 'ES256', null, $header);

        return $token;
    }
}
