<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;

class TokenController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        if (! \Auth::attempt($request->safe()->only('email', 'password'))) {
            return response()->json([
                'message' => __('auth.failed'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $request->user()->createToken('access_token', ['*'], now()->addMinutes(config('sanctum.expiration')))->plainTextToken;

        $cookie = Cookie::make('access_token', $token, config('sanctum.cookie_lifetime'));

        return response()->json([
            'data' => [
                'user' => UserResource::make(\Auth::user()),
                'token' => $token,
            ],
        ])->withCookie($cookie);
    }
}
