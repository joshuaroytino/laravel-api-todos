<?php

namespace App\Listeners;

use App\Notifications\SendSuccessfulPasswordResetNotification;
use Illuminate\Auth\Events\PasswordReset;

class SendPasswordResetNotification
{
    public function handle(PasswordReset $event): void
    {
        $event->user->notify(new SendSuccessfulPasswordResetNotification());
    }
}
