<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function fakeSanctumUser()
    {
        Sanctum::actingAs(User::factory()->createOne());
    }

    protected function createOwnerUser()
    {
        return User::factory()->createOne([
            'email' => 'owner@email.com',
        ]);
    }

    protected function createNonOwnerUser()
    {
        return User::factory()->createOne([
            'email' => 'non-owner@email.com',
        ]);
    }
}
