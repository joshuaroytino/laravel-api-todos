<?php

namespace Tests\Feature;

use App\Http\Resources\UserResource;
use Tests\TestCase;

class TokenControllerTest extends TestCase
{
    public function testShouldReturnToken()
    {
        $owner = $this->createOwnerUser();
        $response = $this->postJson(route('token'), [
            'email' => $owner->email,
            'password' => 'password',
        ]);

        $response->assertOk();

        $this->assertEqualsCanonicalizing(UserResource::make($owner)->response()->getData(true)['data'], $response->json('data.user'));

        $response->assertJsonStructure([
            'data' => [
                'token',
                'user' => [
                    'id',
                    'email',
                    'name',
                ],
            ],
        ]);
    }

    public function testShouldNotReturnTokenIfInvalidCredentials()
    {
        $owner = $this->createOwnerUser();

        $response = $this->postJson(route('token'), [
            'email' => $owner->email,
            'password' => 'incorrect-password',
        ]);

        $response->assertUnauthorized();
        $response->assertJson(['data' => ['message' => 'Invalid credentials']]);
    }
}
