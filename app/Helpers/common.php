<?php

declare(strict_types=1);

use App\Models\PersonalAccessToken;

if (!function_exists('loginUsingBearerToken')) {
    function loginUsingBearerToken(): void
    {
        PersonalAccessToken::loginWithToken(request()->bearerToken());
    }
}
