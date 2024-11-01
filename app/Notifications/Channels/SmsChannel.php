<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification)
    {
        // Check if the notification has the method `toSms()`
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        // Get the phone number and message
        $message = $notification->toSms($notifiable);
        $phoneNumber = $notifiable->routeNotificationFor('sms');

        // code to send sms
    }
}
