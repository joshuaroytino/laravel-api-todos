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
        $todos = Todo::query()
            ->where(['user_id' => \Auth::user()->id])
            ->latest()
            ->get();

        return TodoResource::collection($todos);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(TodoCreateRequest $request): JsonResource
    {
        $this->authorize('create', Todo::class);

        $todo = new Todo();
        $todo->fill(array_merge(
            $request->validated(),
            [
                'user_id' => \Auth::user()->id,
            ])
        );
        $todo->save();

        $todo->refresh();

        return new TodoResource($todo);
    }

    /**
     * @throws \Throwable
     */
    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo);

        $todo->deleteOrFail();

        return response()->noContent();
    }
}
