<?php

namespace App\Services;

use AppleClient;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;

class JwtService
{
    protected string $keyId;
    protected string $issuerId;
    protected string $privateKey;

    public function __construct()
    {
        $this->keyId = config('appstore.key_id'); // ID da chave
        $this->issuerId = config('appstore.issuer_id'); // ID do time
        $this->privateKey = storage_path('app/keys/AuthKey_' . $this->keyId . '.p8');
    }

    /**
     * Gera um JWT para autenticar com a App Store Server API.
     *
     * @return string
     */
    public function generateJwt(): string
    {
        $client = new AppleClient();
        $client = new AppleClient();
        // $client->setApiKey('/Users/viniciusgoulart/Documents/Jobs/Luiz/appleitura/storage/app/keys/AuthKey_G5AFUS4VS9.p8');
        $client->setApiKey($this->privateKey);
        $client->setIssuerId($this->issuerId);
        $client->setKeyIdentifier($this->keyId);

        $token = $client->generateToken();

        return $token;
    }
}
