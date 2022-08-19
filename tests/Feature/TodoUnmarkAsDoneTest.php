<?php

namespace Tests\Feature;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TodoUnmarkAsDoneTest extends TestCase
{
    public function testShouldUnmarkTodoAsDone()
    {
        $owner = $this->createOwnerUser();
        Sanctum::actingAs($owner);

        $todo = Todo::factory()->done()->createOne([
            'user_id' => $owner->id,
        ]);

        $this->assertEquals(1, $todo->done);

        $response = $this->postJson(route('todo.unmark.done', $todo));
        $response->assertOk();

        $actualResponse = $response->json();

        $id = $response->json('data.id');
        $updatedTodo = Todo::find($id);
        $expectedResponse = (new TodoResource($updatedTodo))->additional([
            'message' => 'Todo has been unmarked as done.',
        ])->response()->getData(true);

        $this->assertEquals(0, $updatedTodo->done);
        $this->assertEqualsCanonicalizing($expectedResponse, $actualResponse);
    }

    public function testCannotUnmarkTodoAsDoneIfNotOwned()
    {
        $owner = $this->createOwnerUser();
        $todo = Todo::factory()->createOne([
            'user_id' => $owner->id,
        ]);

        $nonOwner = $this->createNonOwnerUser();
        Sanctum::actingAs($nonOwner);

        $response = $this->postJson(route('todo.unmark.done', $todo));
        $response->assertNotFound();
    }

    public function testOnlyAuthorizedUserCanAccess()
    {
        $owner = $this->createOwnerUser();
        $todo = Todo::factory()->createOne([
            'user_id' => $owner->id,
        ]);

        $response = $this->postJson(route('todo.unmark.done', $todo));
        $response->assertUnauthorized();
    }

    public function testReturnNotFoundIfTodoDoesNotExist()
    {
        $owner = $this->createOwnerUser();
        Sanctum::actingAs($owner);

        $todo = Todo::factory()->done()->createOne([
            'user_id' => $owner->id,
        ]);
        $todo->delete();

        $response = $this->postJson(route('todo.unmark.done', $todo));
        $response->assertNotFound();
    }
}
