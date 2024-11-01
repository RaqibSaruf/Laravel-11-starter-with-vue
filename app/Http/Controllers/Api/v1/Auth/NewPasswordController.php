<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewPasswordRequest;
use Illuminate\Http\JsonResponse as Response;

class NewPasswordController extends Controller
{
    public function store(NewPasswordRequest $request): Response
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
