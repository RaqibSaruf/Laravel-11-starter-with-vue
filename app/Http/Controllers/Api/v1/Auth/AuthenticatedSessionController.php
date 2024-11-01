<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Services\AuthenticatedSessionService;
use Illuminate\Http\JsonResponse as Response;

class AuthenticatedSessionController extends Controller
{
    public function __construct(private AuthenticatedSessionService $service)
    {
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        return response()->json([
            'message' => 'Logged in successfully',
            'data' => $this->service->create(),
        ], Response::HTTP_CREATED);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(LogoutRequest $request): Response
    {
        $this->service->delete($request->logout_from_all_device ?? false);

        return response()->json([
            'message' => $request->logout_from_all_device ? 'Logged out from all device successfully' : 'Logged out successfully',
        ], Response::HTTP_CREATED);
    }
}
