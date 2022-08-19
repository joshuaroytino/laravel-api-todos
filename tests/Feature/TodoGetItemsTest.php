<?php

namespace Tests\Feature;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TodoGetItemsTest extends TestCase
{
    public function testShouldGetListOfTodos()
    {
        $owner = $this->createOwnerUser();

        Sanctum::actingAs($owner);

        Todo::factory(5)->create([
            'user_id' => $owner->id,
        ]);

        $todos = Todo::query()->where(['user_id' => $owner->id])->get();
        $todosResource = TodoResource::collection($todos);
        $expectedResponse = $todosResource->response()->getData(true);

        $response = $this->getJson(route('todos.index'));
        $response->assertOk();

        $actualResponse = $response->json();

        $this->assertEqualsCanonicalizing($expectedResponse, $actualResponse);
    }

    public function testNonOwnerCannotSee()
    {
        $owner = $this->createOwnerUser();

        Todo::factory(5)->create([
            'user_id' => $owner->id,
        ]);

        $nonOwner = $this->createNonOwnerUser();
        Sanctum::actingAs($nonOwner);

        $todos = Todo::query()
            ->where(['user_id' => $nonOwner->id])
            ->latest()
            ->get();
        $todosResource = TodoResource::collection($todos);
        $expectedResponse = $todosResource->response()->getData(true);

        $response = $this->getJson(route('todos.index'));
        $response->assertOk();

        $actualResponse = $response->json();

        $this->assertEqualsCanonicalizing($expectedResponse, $actualResponse);
    }

    public function testOnlyAuthorizedUserCanAccess()
    {
        $response = $this->getJson(route('todos.index'));
        $response->assertUnauthorized();
    }
}
