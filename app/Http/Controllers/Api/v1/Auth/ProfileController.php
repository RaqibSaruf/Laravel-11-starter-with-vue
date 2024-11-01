<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse as Response;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function myProfile(): Response
    {
        $user = Auth::user();
        $user->load([
            'roles:id,name',
            'roles.permissions:id,name',
        ]);

        return response()->json([
            'message' => 'My profile info',
            'data' => $user,
        ], Response::HTTP_OK);
    }

    public function updateProfile(ProfileUpdateRequest $request): Response
    {
        $user = Auth::user();
        $user->update($request->only($user->getFillable()));

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user,
        ], Response::HTTP_CREATED);
    }
}
