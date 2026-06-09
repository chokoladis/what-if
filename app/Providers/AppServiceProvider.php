<?php

namespace App\Providers;

use App\Interfaces\AI\AIClientContract;
use App\Interfaces\AI\ValidatorAvatarContract;
use App\Services\AI\Gemini\AvatarValidatorService;
use App\Services\AI\Gemini\ClientService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ValidatorAvatarContract::class, AvatarValidatorService::class);
        $this->app->singleton(AIClientContract::class, ClientService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('isAdmin', function ($user) {
            return $user->role === 'admin';
        });

        Paginator::useBootstrap();
    }
}
