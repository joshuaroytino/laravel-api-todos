<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::latest()->get();

        return TodoResource::collection($todos);
    }

    public function store(Request $request)
    {
        $todo = new Todo();
        $todo->text = $request->input('text');
        $todo->save();

        return response()->json([
            'message' => 'Todo successfully created.',
        ]);
    }

    public function destroy(Todo $todo)
    {
        $todo->deleteOrFail();

        return response()->json([
            'message' => 'Todo successfully deleted.',
        ]);
    }
}
