<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $attributes = $request->safe()->only('name', 'email', 'password');

        $user = User::create($attributes);

        event(new Registered($user));

        return response()->json([
            'message' => 'Registration is successful and a verification email has been sent to your inbox. Please verify your account to be able to login.',
        ]);
    }
}
