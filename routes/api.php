<?php

use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MarkTodoController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResendVerifyEmailController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UnMarkTodoController;
use App\Http\Controllers\VerifyEmailController;
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

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('todos', [TodoController::class, 'index'])->name('todos.index');
    Route::post('todo', [TodoController::class, 'store'])->name('todo.store');
    Route::delete('todo/{todo}', [TodoController::class, 'destroy'])->name('todo.destroy');
    Route::post('todo/mark/{todo}', MarkTodoController::class)->name('todo.mark.done');
    Route::post('todo/unmark/{todo}', UnMarkTodoController::class)->name('todo.unmark.done');
});

Route::post('token', TokenController::class)
    ->middleware('login_verification')
    ->name('token');
Route::post('logout', LogoutController::class)
    ->middleware('auth:sanctum')
    ->name('logout');

Route::post('register', RegisterController::class)->name('register');
Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->name('verification.verify');
Route::post('email/verification-notification', ResendVerifyEmailController::class)
    ->middleware('throttle:6,1')
    ->name('verification.send');
