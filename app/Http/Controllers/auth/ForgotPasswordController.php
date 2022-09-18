<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        $attributes = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $attributes['email'])->first();

        if ($user->exists() && $user->hasVerifiedEmail()) {
            Password::sendResetLink(['email' => $attributes['email']]);
        }

        return response()->json(['message' => 'If you have records in the system, a reset password link will be sent to your inbox.']);
    }
}
