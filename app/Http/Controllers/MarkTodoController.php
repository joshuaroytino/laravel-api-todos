<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoResource;
use App\Models\Todo;

class MarkTodoController extends Controller
{
    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function __invoke(Todo $todo)
    {
        $this->authorize('update', $todo);

        $todo->done = true;
        $todo->save();
        $todo->refresh();

        return (new TodoResource($todo))->additional([
            'message' => 'Todo has been marked as done.',
        ]);
    }
}
