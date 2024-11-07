<?php

declare(strict_types=1);

use App\Http\Controllers\Api\v1\PermissionController;
use App\Http\Controllers\Api\v1\RoleController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->group(function () {
        Route::middleware(['auth:sanctum', 'verified', 'throttle:60,1'])
            ->group(function () {
                Route::apiResource('roles', RoleController::class);
                Route::get('/role/list', [RoleController::class, 'list']);
                Route::get('/permission/list', PermissionController::class);
                Route::apiResource('users', UserController::class);
            });
    });

require __DIR__ . '/auth.php';
