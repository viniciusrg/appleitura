<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppleTransaction extends Model
{
    use HasFactory;

    protected $table = 'apple_transactions';

    protected $fillable = [
        'transactionId',
        'user_id',
        'inAppOwnershipType',
        'subscriptionGroupIdentifier',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
