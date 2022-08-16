<?php

namespace Tests\Feature;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Tests\TestCase;

class TodoUnmarkAsDoneTest extends TestCase
{
    public function testShouldUnmarkTodoAsDone()
    {
        $todo = Todo::factory()->done()->createOne();

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

    public function testReturnNotFoundIfTodoDoesNotExist()
    {
        $todo = Todo::factory()->done()->createOne();
        $todo->delete();

        $response = $this->postJson(route('todo.unmark.done', $todo));
        $response->assertNotFound();
    }
}
