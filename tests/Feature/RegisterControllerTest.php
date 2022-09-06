<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    public function testShouldBeAbleToRegister()
    {
        Event::fake();

        $registrant = $this->validData();

        Event::assertNothingDispatched();

        $this->postJson(route('register'), $registrant)
            ->assertOk();

        $this->assertDatabaseHas('users', ['email' => $registrant['email']]);

        Event::assertDispatched(Registered::class);
        Event::assertDispatchedTimes(Registered::class, 1);
    }

    /**
     * @dataProvider validationProvider
     */
    public function testShouldValidateData($getData)
    {
        [$field, $payload] = $getData();

        $this->postJson(route('register'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    }

    private function validationProvider(): array
    {
        return [
            'it fails if payload is empty' => [
                function () {
                    return [
                        ['name', 'email', 'password', 'confirm_password'],
                        [],
                    ];
                },
            ],
            'it fails if name is empty string' => [
                function () {
                    return [
                        'name',
                        array_merge($this->validData(), ['name' => '']),
                    ];
                },
            ],
            'it fails if name is more than 255 characters' => [
                function () {
                    return [
                        'name',
                        array_merge($this->validData(), ['name' => str_repeat('a', 256)]),
                    ];
                },
            ],
            'it fails if email is empty string' => [
                function () {
                    return [
                        'email',
                        array_merge($this->validData(), ['email' => '']),
                    ];
                },
            ],
            'it fails if email is not valid email' => [
                function () {
                    return [
                        'email',
                        array_merge($this->validData(), ['email' => 'not-a-valid-email']),
                    ];
                },
            ],
            'it fails if email is already registered' => [
                function () {
                    User::factory()->createOne([
                        'email' => $this->validData()['email'],
                    ]);

                    return [
                        'email',
                        $this->validData(),
                    ];
                },
            ],
            'it fails if email is more than 255 characters' => [
                function () {
                    $emailWithMoreThan255Characters = str_repeat('a', 256 - strlen('@email.com')).'@email.com';

                    return [
                        'email',
                        array_merge($this->validData(), ['email' => $emailWithMoreThan255Characters]),
                    ];
                },
            ],
            'it fails if password is empty string' => [
                function () {
                    return [
                        'password',
                        array_merge($this->validData(), ['password' => '']),
                    ];
                },
            ],
            'it fails if password is less than 8 characters' => [
                function () {
                    return [
                        'password',
                        array_merge($this->validData(), ['password' => str_repeat('a', 7)]),
                    ];
                },
            ],
            'it fails if confirm_password is empty string' => [
                function () {
                    return [
                        'confirm_password',
                        array_merge($this->validData(), ['confirm_password' => '']),
                    ];
                },
            ],
            'it fails if confirm_password is not similar with password' => [
                function () {
                    return [
                        'confirm_password',
                        array_merge($this->validData(), ['confirm_password' => 'password_mismatch']),
                    ];
                },
            ],
        ];
    }

    private function validData(): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'john.doe@email.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ];
    }
}
