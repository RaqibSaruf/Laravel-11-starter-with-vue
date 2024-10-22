<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Notifications\SendVerificationOtp;
use App\Services\OtpTokenManagerService;

class SendVerificationNotification
{
    public function handle(UserRegistered $event)
    {
        $user = $event->user;

        $otpVerificationToken = (new OtpTokenManagerService($user->email))->create();

        $user->notify(new SendVerificationOtp($otpVerificationToken));
    }
}
