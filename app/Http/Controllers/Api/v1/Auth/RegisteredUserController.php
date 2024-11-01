<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1\Auth;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisteredUserRequest;
use App\Models\User;
use App\Services\AuthenticatedSessionService;
use Illuminate\Http\JsonResponse as Response;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function __construct(private AuthenticatedSessionService $service)
    {
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisteredUserRequest $request): Response
    {
        $user = new User();
        $user->fill($request->validated())
            ->save();

        Auth::login($user);

        event(new UserRegistered($user));

        return response()->json([
            'message' => 'Registration successfully completed.',
            'data' => $this->service->create(),
        ], Response::HTTP_CREATED);
    }
}
