<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

class PasswordSecurityService
{
    public function isReused($user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}
