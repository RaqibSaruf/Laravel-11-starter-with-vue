<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionService
{
    public function create(): User
    {
        $user = Auth::user();
        $user->is_verified = $user->isVerified();
        $tokenExpiredAt = now()->addMonth();
        $user->token = $user->createToken(
            'access-token',
            ['*'],
            $tokenExpiredAt
        )->plainTextToken;
        $user->token_expired_at = $tokenExpiredAt;

        return $user;
    }

    public function delete($deleteAll = false)
    {
        $user = Auth::user();
        if ($deleteAll) {
            return $user->tokens()->delete();
        }

        return $user->currentAccessToken()->delete();
    }
}
