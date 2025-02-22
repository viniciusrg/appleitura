<?php

namespace App\Services;

use App\Models\User;

class UserCategoryServices
{
    public static function getCategoryIds(User $user)
    {
        return $user->subcategories->pluck('id')->toArray();
    }
}
