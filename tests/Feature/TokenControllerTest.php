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
