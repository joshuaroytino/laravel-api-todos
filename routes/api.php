<?php

use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MarkTodoController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TokenController;
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

Route::get('todos', [TodoController::class, 'index'])->name('todos.index')->middleware('auth:sanctum');
Route::post('todo', [TodoController::class, 'store'])->name('todo.store')->middleware('auth:sanctum');
Route::delete('todo/{todo}', [TodoController::class, 'destroy'])->name('todo.destroy')->middleware('auth:sanctum');
Route::post('todo/mark/{todo}', MarkTodoController::class)->name('todo.mark.done')->middleware('auth:sanctum');
Route::post('todo/unmark/{todo}', UnMarkTodoController::class)->name('todo.unmark.done')->middleware('auth:sanctum');

Route::post('token', TokenController::class)->name('token');
Route::post('logout', LogoutController::class)->name('logout')->middleware('auth:sanctum');
