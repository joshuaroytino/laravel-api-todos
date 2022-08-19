<?php

namespace Tests\Feature;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TodoMarkAsDoneTest extends TestCase
{
    public function testShouldMarkTodoAsDone()
    {
        $owner = User::factory()->createOne([
            'email' => 'owner@email.com',
        ]);

        Sanctum::actingAs($owner);

        $todo = Todo::factory()->notDone()->createOne([
            'user_id' => $owner->id,
        ]);

        $this->assertEquals(0, $todo->done);
        $this->assertEquals($owner->id, $todo->user_id);

        $response = $this->postJson(route('todo.mark.done', $todo));
        $response->assertOk();

        $actualResponse = $response->json();

        $id = $response->json('data.id');
        $updatedTodo = Todo::find($id);
        $expectedResponse = (new TodoResource($updatedTodo))->additional([
            'message' => 'Todo has been marked as done.',
        ])->response()->getData(true);

        $this->assertEquals(1, $updatedTodo->done);
        $this->assertEqualsCanonicalizing($expectedResponse, $actualResponse);
    }

    public function testCannotMarkTodoAsDoneIfNotOwned()
    {
        $owner = $this->createOwnerUser();

        $todo = Todo::factory()->createOne([
            'user_id' => $owner->id,
        ]);

        $nonOwner = $this->createNonOwnerUser();

        Sanctum::actingAs($nonOwner);

        $response = $this->postJson(route('todo.mark.done', $todo));
        $response->assertNotFound();
    }

    public function testOnlyAuthorizedUserCanAccess()
    {
        $owner = $this->createNonOwnerUser();

        $todo = Todo::factory()->createOne([
            'user_id' => $owner->id,
        ]);

        $response = $this->postJson(route('todo.mark.done', $todo));
        $response->assertUnauthorized();
    }

    public function testReturnNotFoundIfTodoDoesNotExist()
    {
        $owner = $this->createOwnerUser();

        Sanctum::actingAs($owner);

        $todo = Todo::factory()->notDone()->createOne(['user_id' => $owner->id]);
        $todo->delete();

        $response = $this->postJson(route('todo.mark.done', $todo));
        $response->assertNotFound();
    }
}
