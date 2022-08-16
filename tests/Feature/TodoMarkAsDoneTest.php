<?php

namespace Tests\Feature;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Tests\TestCase;

class TodoMarkAsDoneTest extends TestCase
{
    public function testShouldMarkTodoAsDone()
    {
        $todo = Todo::factory()->notDone()->createOne();

        $this->assertEquals(0, $todo->done);

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

    public function testReturnNotFoundIfTodoDoesNotExist()
    {
        $todo = Todo::factory()->notDone()->createOne();
        $todo->delete();

        $response = $this->postJson(route('todo.mark.done', $todo));
        $response->assertNotFound();
    }
}
