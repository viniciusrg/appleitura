<?php

namespace App\Services;

use Firebase\JWT\JWT;

class JwtService
{
    protected string $keyId;
    protected string $issuerId;
    protected string $privateKey;

    public function __construct()
    {
        $this->keyId = config('appstore.key_id'); // ID da chave
        $this->issuerId = config('appstore.issuer_id'); // ID do time
        $this->privateKey = file_get_contents(storage_path('app/keys/SubscriptionKey_' . $this->keyId . '.p8'));
    }

    /**
     * Gera um JWT para autenticar com a App Store Server API.
     *
     * @return string
     */
    public function generateJwt(): string
    {
        $claims = [
            'iss' => $this->issuerId,           // Issuer ID
            'iat' => time(),                    // Emitido em
            'exp' => time() + 3600,             // Expira em 1 hora
            'aud' => 'appstoreconnect-v1',      // Audiência
        ];

        return JWT::encode($claims, $this->privateKey, 'ES256', $this->keyId);
    }
}

