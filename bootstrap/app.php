<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, \Throwable $e) {
            return $request->expectsJson() || $request->is('api/*');
        });
        $exceptions->render(function (ModelNotFoundException $exception, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => config('app.debug') === true ? $exception->getMessage() : 'Data not found!'
                ], Response::HTTP_NOT_FOUND);
            }
        });
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });
        $exceptions->render(function (AuthenticationException|UnauthorizedException $exception, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], Response::HTTP_UNAUTHORIZED);
            }
        });
        $exceptions->render(function (\Exception|\Throwable $exception, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $code = $exception->getCode();
                if (!config('app.debug', false)) {
                    return response()->json([
                        'message' => 'Your request can not process at this momment, Please try again!',
                    ], $code && is_int($code) ? $code : Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                return response()->json([
                    'message' => $exception->getMessage(),
                    'debug' => [
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'trace' => $exception->getTrace(),
                    ],
                ], $code && is_int($code) ? $code : Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    })->create();
