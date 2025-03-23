<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'provider',         // 'google_play', 'apple', etc.
        'product_id',       // ID do produto na loja (ex: vida_positiva_mensal)
        'purchase_token',   // Token da compra na plataforma
        'status',           // 'active', 'expired', 'canceled', etc.
        'expires_at',       // Data de expiração
        'auto_renewing',    // Se renova automaticamente
        'payment_state',    // Estado do pagamento na plataforma
        'cancel_reason',    // Motivo do cancelamento, se houver
        'provider_data',    // Dados completos da plataforma (JSON)
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'auto_renewing' => 'boolean',
    ];

    // Relacionamento com o usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Verifica se a assinatura está ativa
    public function isActive()
    {
        return $this->status === 'active';
    }
}

