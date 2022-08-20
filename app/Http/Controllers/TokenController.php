<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Carbon\Carbon;
use Illuminate\Http\Response;

class TokenController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        if (! \Auth::attempt($request->safe()->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $expirationAt = ! is_null(config('sanctum.expiration')) ? Carbon::now()->addMinutes(config('sanctum.expiration')) : null;

        $token = $request->user()->createToken('access_token', ['*'], $expirationAt)->plainTextToken;

        $cookie = cookie('access_token', $token, config('sanctum.expiration'));

        return response()->json([
            'user' => \Auth::user(),
            'token' => $token,
        ])->withCookie($cookie);
    }
}
