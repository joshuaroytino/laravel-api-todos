<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifyEmailControllerTest extends TestCase
{
    public function testUserCanBeVerified()
    {
        $user = User::factory()->unverified()->createOne();

        $this->assertFalse(User::find($user->id)->hasVerifiedEmail());

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->get($verificationUrl)
            ->assertOk();

        $this->assertTrue(User::find($user->id)->hasVerifiedEmail());
    }
}
