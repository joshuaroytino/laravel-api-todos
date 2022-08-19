<?php

namespace Tests\Feature;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoAddTest extends TestCase
{
    use WithFaker;

    public function testShouldBeAbleToAddTodo()
    {
        $this->fakeSanctumUser();

        $todoData = Todo::factory()->makeOne()->getAttributes();

        $response = $this->postJson(route('todo.store'), $todoData);
        $response->assertCreated();

        $id = $response->json('data.id');
        $todoItem = Todo::find($id);
        $expectedResponse = (new TodoResource($todoItem))->response()->getData(true);

        $actualResponse = $response->json();

        $this->assertEqualsCanonicalizing($expectedResponse, $actualResponse);
    }

    /**
     * @dataProvider validationProvider
     */
    public function testShouldValidateTodoData($getData)
    {
        $this->fakeSanctumUser();

        [$field, $payload] = $getData();

        $response = $this->postJson(route('todo.store'), $payload);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors($field);
    }

    public function validationProvider(): array
    {
        return [
            'it fails if text is more than 255 characters' => [
                function () {
                    return [
                        'text',
                        array_merge($this->validData(), ['text' => str_repeat('a', 256)]),
                    ];
                },
            ],
            'it fails if text is empty string' => [
                function () {
                    return [
                        'text',
                        array_merge($this->validData(), ['text' => '']),
                    ];
                },
            ],
            'it fails if text is null' => [
                function () {
                    return [
                        'text',
                        array_merge($this->validData(), ['text' => null]),
                    ];
                },
            ],
            'it fails if text is empty array' => [
                function () {
                    return [
                        'text',
                        array_merge($this->validData(), ['text' => []]),
                    ];
                },
            ],
        ];
    }

    public function testCannotAddTodoIfUnauthenticated()
    {
        $response = $this->postJson(route('todo.store'));

        $response->assertUnauthorized();
    }

    protected function validData(): array
    {
        return Todo::factory()->makeOne()->getAttributes();
    }
}
