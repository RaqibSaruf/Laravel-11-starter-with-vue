<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\SendVerificationOtp;
use App\Services\OtpTokenManagerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->isVerified()) {
            throw new \Exception("Your account is already verified");
        }

        $otpVerificationToken = (new OtpTokenManagerService($user->email))->create();

        $user->notify(new SendVerificationOtp($otpVerificationToken));

        return response()->json(['message' => 'Verification code sent successfully!'], Response::HTTP_OK);
    }
}
