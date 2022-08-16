<?php

namespace App\Http\Controllers;

use App\Http\Requests\TodoCreateRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoController extends Controller
{
    public function index(): JsonResource
    {
        $todos = Todo::latest()->get();

        return TodoResource::collection($todos);
    }

    public function store(TodoCreateRequest $request): JsonResource
    {
        $todo = Todo::create($request->validated());

        $todo->refresh();

        return new TodoResource($todo);
    }

    /**
     * @throws \Throwable
     */
    public function destroy(Todo $todo)
    {
        $todo->deleteOrFail();

        return response()->noContent();
    }
}
