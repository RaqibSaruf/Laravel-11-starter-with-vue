<?php

declare(strict_types=1);

use App\Http\Controllers\Api\v1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\v1\Auth\ChangePasswordController;
use App\Http\Controllers\Api\v1\Auth\NewPasswordController;
use App\Http\Controllers\Api\v1\Auth\PasswordResetController;
use App\Http\Controllers\Api\v1\Auth\RegisteredUserController;
use App\Http\Controllers\Api\v1\Auth\VerificationNotificationController;
use App\Http\Controllers\Api\v1\Auth\VerifyController;
use App\Http\Controllers\Api\v1\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/forgot-password', [PasswordResetController::class, 'store'])
    ->middleware('guest', 'throttle:6,1')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::put('/change-password', ChangePasswordController::class)
    ->middleware(['auth:sanctum']);

Route::post('/verify', VerifyController::class)
    ->middleware(['auth:sanctum', 'throttle:6,1']);

Route::post('/send-verification-notification', [VerificationNotificationController::class, 'store'])
    ->middleware(['auth:sanctum', 'throttle:6,1']);

Route::delete('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');

Route::get('/my-profile', [ProfileController::class, 'myProfile'])->middleware('auth:sanctum');
Route::put('/my-profile', [ProfileController::class, 'updateProfile'])->middleware('auth:sanctum');
