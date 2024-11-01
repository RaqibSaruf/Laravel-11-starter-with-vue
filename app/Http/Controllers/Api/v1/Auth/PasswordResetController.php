<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Notifications\SendPasswordResetOtp;
use App\Services\OtpTokenManagerService;
use Illuminate\Http\JsonResponse as Response;

class PasswordResetController extends Controller
{
    public function store(PasswordResetRequest $request): Response
    {
        $user = $request->findUser();
        $otpVerificationToken = (new OtpTokenManagerService($user->email))->create();

        $user->notify(new SendPasswordResetOtp($otpVerificationToken));

        return response()->json([
            'message' => 'Password reset code sent successfully',
            'data' => [
                'email' => $user->email,
                'token' => $otpVerificationToken->token,
            ],
        ], Response::HTTP_CREATED);
    }
}
