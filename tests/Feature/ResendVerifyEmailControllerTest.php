<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResendVerifyEmailControllerTest extends TestCase
{
    public function testShouldBeAbleToSendVerificationEmail()
    {
        Notification::fake();

        $user = User::factory()->createOne();

        Notification::assertNothingSent();

        $this->postJson(route('verification.send'), [
            'email' => $user->email,
        ])->assertOk();

        Notification::assertSentTo($user, VerifyEmail::class);
        Notification::assertTimesSent(1, VerifyEmail::class);
    }
}
