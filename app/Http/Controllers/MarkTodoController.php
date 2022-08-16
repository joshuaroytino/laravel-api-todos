<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoResource;
use App\Models\Todo;

class MarkTodoController extends Controller
{
    public function __invoke(Todo $todo)
    {
        $todo->done = true;
        $todo->save();
        $todo->refresh();

        return (new TodoResource($todo))->additional([
            'message' => 'Todo has been marked as done.',
        ]);
    }
}
