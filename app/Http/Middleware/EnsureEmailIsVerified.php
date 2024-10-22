<?php

namespace App\Http\Middleware;

use App\Traits\Interfaces\VerifyAccount;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! $request->user() ||
            ($request->user() instanceof VerifyAccount &&
                ! $request->user()->isVerified())
        ) {
            return response()->json(['message' => 'Your account is not verified.'], HttpResponse::HTTP_CONFLICT);
        }

        return $next($request);
    }
}
