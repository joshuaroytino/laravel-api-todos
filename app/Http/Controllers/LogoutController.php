<?php

namespace App\Http\Controllers;

use Cookie;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        Cookie::queue(Cookie::forget('access_token'));

        return response()->noContent();
    }
}
