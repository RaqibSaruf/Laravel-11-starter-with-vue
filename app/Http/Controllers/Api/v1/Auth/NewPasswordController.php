<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class NewPasswordController extends Controller
{
    public function store(NewPasswordRequest $request): JsonResponse
    {
        if ($request->verify()) {
            $user = $request->findUser();

            $user->update(['password' => $request->password]);

            return response()->json([
                'message' => 'Password reset successfully',
            ], Response::HTTP_CREATED);
        }

        throw new \Exception('Request failed', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
