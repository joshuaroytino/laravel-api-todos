<?php

namespace Tests\Feature;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Tests\TestCase;

class TodoGetItemsTest extends TestCase
{
    public function testShouldGetListOfTodos()
    {
        Todo::factory(5)->create();

        $todos = Todo::latest()->get();
        $todosResource = TodoResource::collection($todos);
        $expectedResponse = $todosResource->response()->getData(true);

        $response = $this->get(route('todos.index'));
        $response->assertOk();

        $actualResponse = $response->json();

        $this->assertEqualsCanonicalizing($expectedResponse, $actualResponse);
    }
}
