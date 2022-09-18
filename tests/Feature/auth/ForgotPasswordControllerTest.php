<?php

namespace Tests\Feature\auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    public function testCanSendPasswordResetEmail()
    {
        Notification::fake();

        $user = User::factory()->createOne();

        Notification::assertNothingSent();

        $this->postJson(route('forgot.password'), ['email' => $user->email])
            ->assertOk();

        Notification::assertSentTo($user, ResetPassword::class);
        Notification::assertTimesSent(1, ResetPassword::class);
    }

    public function testCannotSendPasswordResetEmailToUnverifiedUser()
    {
        Notification::fake();

        $user = User::factory()->unverified()->createOne();

        Notification::assertNothingSent();

        $this->postJson(route('forgot.password'), ['email' => $user->email])
            ->assertOk();

        Notification::assertNothingSent();
    }
}
