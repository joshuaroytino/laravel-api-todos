<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ResendVerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        $attributes = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $attributes['email'])->first();

        if ($user->exists()) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json(['message' => 'If you have records in the system, a verification email will be sent to your inbox.']);
    }
}
