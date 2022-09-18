<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Todo;
use App\Models\User;
use App\Policies\TodoPolicy;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\URL;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Todo::class => TodoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::createUrlUsing(function (User $notifiable) {
            $params = [
                'expires' => Carbon::now()
                    ->addMinutes(60)
                    ->getTimestamp(),
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ];

            ksort($params);

            //API url for verification
            $url = URL::route('verification.verify', $params, true);

            $key = config('app.key');
            $signature = hash_hmac('sha256', $url, $key);

            return config('frontend.url').
                '/auth/verify-email/'.
                $params['id'].
                '/'.
                $params['hash'].
                '?'.
                http_build_query([
                    'expires' => $params['expires'],
                    'signature' => $signature,
                ]);
        });

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('frontend.url').
                '/auth/reset-password?'.
                http_build_query([
                    'token' => $token,
                ]);
        });
    }
}
