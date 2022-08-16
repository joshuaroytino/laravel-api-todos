<?php

namespace App\Http\Controllers;

use App\Models\Todo;

class MarkTodoController extends Controller
{
    public function __invoke(Todo $todo)
    {
        $todo->done = true;
        $todo->save();

        return response()->json([
            'message' => 'Todo has been marked as done.',
        ]);
    }
}
