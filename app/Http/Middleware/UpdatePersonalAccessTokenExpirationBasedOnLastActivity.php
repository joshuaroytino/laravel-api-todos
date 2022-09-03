<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdatePersonalAccessTokenExpirationBasedOnLastActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->update(['expires_at' => now()->addMinutes(config('sanctum.expiration'))]);
        }

        return $next($request);
    }
}
