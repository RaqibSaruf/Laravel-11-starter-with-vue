<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use Illuminate\Http\JsonResponse as Response;
use Illuminate\Support\Facades\Auth;

class ChangePasswordController extends Controller
{
    public function __invoke(ChangePasswordRequest $request): Response
    {
        $user = Auth::user();
        $user->update([$request->only(['password'])]);

        return response()->json([
            'message' => 'Password changed successfully',
        ], Response::HTTP_CREATED);
    }
}
