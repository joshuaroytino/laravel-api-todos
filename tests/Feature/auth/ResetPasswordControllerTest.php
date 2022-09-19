<?php

namespace Tests\Feature\auth;

use App\Listeners\SendPasswordResetNotification;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    public function testShouldBeAbleToResetPassword()
    {
        Event::fake();
        Notification::fake();

        $newPassword = 'new-password';
        $oldPassword = 'password';

        $user = User::factory()->createOne();
        $token = Password::createToken($user);

        $this->postJson('api/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])
            ->assertOk();

        $user->refresh();

        $this->assertFalse(Hash::check($oldPassword, $user->password));
        $this->assertTrue(Hash::check($newPassword, $user->password));

        Event::assertDispatched(PasswordReset::class);
        Event::assertDispatchedTimes(PasswordReset::class);

        Event::assertListening(
            PasswordReset::class,
            SendPasswordResetNotification::class
        );
    }
}
