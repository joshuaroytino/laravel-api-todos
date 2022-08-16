<?php

namespace App\Http\Controllers;

use App\Models\Todo;

class UnMarkTodoController extends Controller
{
    public function __invoke(Todo $todo)
    {
        $todo->done = false;
        $todo->save();

        return response()->json([
            'message' => 'Todo has been unmarked as done.',
        ]);
    }
}
