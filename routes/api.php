<?php

use App\Http\Controllers\MarkTodoController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UnMarkTodoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('todos', [TodoController::class, 'index'])->name('todos.index');
Route::post('todo', [TodoController::class, 'store'])->name('todo.store');
Route::delete('todo/{todo}', [TodoController::class, 'destroy']);
Route::post('todo/mark/{todo}', MarkTodoController::class);
Route::post('todo/unmark/{todo}', UnMarkTodoController::class);
