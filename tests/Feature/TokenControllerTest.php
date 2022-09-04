<?php

namespace Tests\Feature;

use App\Http\Resources\UserResource;
use App\Models\User;
use Tests\TestCase;

class TokenControllerTest extends TestCase
{
    public function testShouldReturnToken()
    {
        $user = User::factory()->createOne();
        $response = $this->postJson(route('token'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk();

        $this->assertEqualsCanonicalizing(UserResource::make($user)->response()->getData(true)['data'], $response->json('data.user'));

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
        $response->assertJson(['message' => __('auth.failed')]);
    }

    public function testShouldNotBeAbleToLoginIfUnverified()
    {
        $user = User::factory()->unverified()->createOne();

        $this->postJson(route('token'), [
            'email' => $user->email,
            'password' => 'password'
        ])
            ->assertForbidden()
            ->assertJson(['message' => __('auth.unverified')]);
    }

    /**
     * @dataProvider validationProvider
     */
    public function testShouldValidateLoginData($getData)
    {
        [$field, $payload] = $getData();

        $response = $this->postJson(route('token'), $payload);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors($field);
    }

    public function validationProvider(): array
    {
        return [
            'it fails if email is not a valid email' => [
                function () {
                    return [
                        'email',
                        array_merge($this->validData(), ['email' => 'not-an-email']),
                    ];
                },
            ],
            'it fails if email is empty' => [
                function () {
                    return [
                        'email',
                        array_merge($this->validData(), ['email' => '']),
                    ];
                },
            ],
            'it fails if password is empty' => [
                function () {
                    return [
                        'password',
                        array_merge($this->validData(), ['password' => '']),
                    ];
                },
            ],
        ];
    }

    public function validData(): array
    {
        return [
            'email' => 'user@email.com',
            'password' => 'password',
        ];
    }
}
