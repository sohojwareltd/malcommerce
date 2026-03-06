<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register SMS Service as singleton
        $this->app->singleton(\App\Services\SmsService::class, function ($app) {
            return new \App\Services\SmsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('role', function ($value) {
            return Role::where('guard_name', config('auth.defaults.guard'))->findOrFail($value);
        });
    }
}
