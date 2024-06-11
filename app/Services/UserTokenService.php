<?php

namespace App\Services;

use App\Models\User;

class UserTokenService
{
    /**
     * @param User $user
     * @return array
     */
    public function createToken(User $user): array
    {
        $token = $user->createToken('login_token');

        return ['token' => $token->plainTextToken];
    }
}
