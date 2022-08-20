<?php

namespace Tests\Feature;

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
    }
}
