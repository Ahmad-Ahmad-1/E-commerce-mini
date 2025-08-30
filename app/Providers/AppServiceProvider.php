<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // $this->app->singleton(LoginResponse::class, function () {
        //     return new class implements LoginResponse {
        //         public function toResponse($request)
        //         {
        //             return response()->json([
        //                 'message' => 'You have been successfully logged in.'
        //             ]);
        //         }
        //     };
        // });

        // $this->app->singleton(RegisterResponse::class, function () {
        //     return new class implements RegisterResponse {
        //         public function toResponse($request)
        //         {
        //             return response()->json([
        //                 'message' => 'You have been successfully registered.',
        //             ]);
        //         }
        //     };
        // });
    }

    public function boot(): void
    {
        Model::preventLazyLoading();
        // Model::automaticallyEagerLoadRelationships();
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return 'https://localhost:3000/reset-password?token=' . $token .
                '&email=' . urlencode($notifiable->getEmailForPasswordReset());
        });
    }
}
