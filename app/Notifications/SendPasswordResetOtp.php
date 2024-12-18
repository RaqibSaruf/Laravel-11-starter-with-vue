<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\OtpVerificationToken;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class SendPasswordResetOtp extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(private OtpVerificationToken $otpVerificationToken)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(Lang::get('Reset Password'))
            ->markdown('emails.password-reset-notification', ['name' => $notifiable->name, 'otp' => $this->otpVerificationToken->otp]);
    }
}
