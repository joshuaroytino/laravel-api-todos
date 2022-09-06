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

        $verificationUrl = $this->verificationUrl($user);

        $this->getJson($verificationUrl)
            ->assertOk()
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'Verification complete.']);

        $this->assertTrue(User::find($user->id)->hasVerifiedEmail());
    }

    public function testFailIfAlreadyVerified()
    {
        $user = User::factory()->createOne();

        $this->assertTrue(User::find($user->id)->hasVerifiedEmail());

        $verificationUrl = $this->verificationUrl($user);

        $this->getJson($verificationUrl)
            ->assertStatus(400)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'Given email is already verified.']);
    }

    public function testFailIfVerificationHashIsInvalid()
    {
        $user = User::factory()->unverified()->createOne();

        $this->assertFalse(User::find($user->id)->hasVerifiedEmail());

        $invalidVerificationUrl = $this->verificationUrlWithInvalidHash($user);

        $this->getJson($invalidVerificationUrl)
            ->assertForbidden();

        $this->assertFalse(User::find($user->id)->hasVerifiedEmail());
    }

    private function verificationUrl($user): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
    }

    private function verificationUrlWithInvalidHash($user): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );
    }
}
