<?php

namespace Tests\Feature;

use App\Models\Todo;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TodoDeleteTest extends TestCase
{
    public function testBeAbleToDeleteTodo()
    {
        $owner = $this->createOwnerUser();
        Sanctum::actingAs($owner);

        $todo = Todo::factory()->createOne([
            'user_id' => $owner->id,
        ]);

        $response = $this->deleteJson(route('todo.destroy', $todo));
        $response->assertNoContent();

        $this->assertNull(Todo::find($todo->id));
    }

    public function testCannotDeleteIfNotOwned()
    {
        $owner = $this->createOwnerUser();
        $todo = Todo::factory()->createOne([
            'user_id' => $owner->id,
        ]);

        $nonOwner = $this->createNonOwnerUser();
        Sanctum::actingAs($nonOwner);

        $response = $this->deleteJson(route('todo.destroy', $todo));
        $response->assertNotFound();
    }

    public function testOnlyAuthorizedUserCanAccess()
    {
        $owner = $this->createOwnerUser();
        $todo = Todo::factory()->createOne([
            'user_id' => $owner->id,
        ]);

        $response = $this->deleteJson(route('todo.destroy', $todo));
        $response->assertUnauthorized();
    }

    public function testReturnNotFoundIfTodoDoesNotExist()
    {
        $owner = $this->createOwnerUser();
        Sanctum::actingAs($owner);

        $todo = Todo::factory()->createOne([
            'user_id' => $owner->id,
        ]);
        $todo->delete();

        $response = $this->deleteJson(route('todo.destroy', $todo));
        $response->assertNotFound();
    }
}
