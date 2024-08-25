<?php

namespace App\Services;

use App\Models\User;

class UserAdminServices
{
    public static function isAdmin(User $user)
    {
        return $user->permission()->pluck('type')->first();
    }
}
