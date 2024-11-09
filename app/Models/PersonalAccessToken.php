<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    public static function findTokenableId(?string $token): ?int
    {
        if (!empty($token)) {
            if (strpos($token, '|') === false) {
                $instance = static::select('id', 'tokenable_id', 'tokenable_type', 'token')
                    ->where('token', hash('sha256', $token))
                    ->first();

                return $instance ? $instance->tokenable_id : null;
            }

            [$id, $token] = explode('|', $token, 2);

            if ($instance = static::select('id', 'tokenable_id', 'tokenable_type', 'token')->find($id)) {
                return hash_equals($instance->token, hash('sha256', $token)) ? $instance->tokenable_id : null;
            }
        }

        return null;
    }

    public static function loginWithToken(?string $token): void
    {
        if (!empty($token)) {
            if (strpos($token, '|') === false) {
                $instance = static::select('id', 'tokenable_id', 'tokenable_type', 'token')
                    ->where('token', hash('sha256', $token))
                    ->first();
                $tokenableId = $instance ? $instance->tokenable_id : null;
                if ($tokenableId) {
                    Auth::loginUsingId($tokenableId);
                }

                return;
            }

            [$id, $token] = explode('|', $token, 2);

            if ($instance = static::select('id', 'tokenable_id', 'tokenable_type', 'token')->find($id)) {
                $tokenableId = hash_equals($instance->token, hash('sha256', $token)) ? $instance->tokenable_id : null;
                if ($tokenableId) {
                    Auth::loginUsingId($tokenableId);
                }
            }
        }
    }
}
