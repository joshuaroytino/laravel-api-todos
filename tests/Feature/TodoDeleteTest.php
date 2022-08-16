<?php

namespace Tests\Feature;

use App\Models\Todo;
use Tests\TestCase;

class TodoDeleteTest extends TestCase
{
    public function testBeAbleToDeleteTodo()
    {
        $todo = Todo::factory()->createOne();

        $response = $this->deleteJson(route('todo.destroy', $todo));
        $response->assertNoContent();

        $this->assertNull(Todo::find($todo->id));
    }

    public function testReturnNotFoundIfTodoDoesNotExist()
    {
        $todo = Todo::factory()->createOne();
        $todo->delete();

        $response = $this->deleteJson(route('todo.destroy', $todo));
        $response->assertNotFound();
    }
}
