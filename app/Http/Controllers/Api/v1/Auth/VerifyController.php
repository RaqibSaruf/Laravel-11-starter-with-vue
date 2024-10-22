<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class VerifyController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(VerificationRequest $request): JsonResponse
    {
        if ($request->user()->isVerified()) {
            throw new \Exception("Your account is already verified");
        }

        if ($request->verify()) {
            return response()->json([
                'message' => 'Account verified successfully',
            ], Response::HTTP_OK);
        }

        throw new \Exception('Request failed', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
