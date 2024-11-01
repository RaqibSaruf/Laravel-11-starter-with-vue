<?php

declare(strict_types=1);

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

if (!function_exists('loginUsingBearerToken')) {
    function loginUsingBearerToken(): void
    {
        if (!empty(request()->bearerToken())) {
            $personalAccessToken = PersonalAccessToken::select('id', 'tokenable_type', 'tokenable_id')
                ->findToken(request()->bearerToken());

            if (!empty($personalAccessToken) && $personalAccessToken->tokenable_type === User::class) {
                Auth::loginUsingId($personalAccessToken->tokenable_id);
            }
        }
    }
}
