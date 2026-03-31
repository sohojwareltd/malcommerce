<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register SMS Service as singlsaton 
        $this->app->singleton(\App\Services\SmsService::class, function ($app) {
            return new \App\Services\SmsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = (string) config('app.url');

        if (str_starts_with($appUrl, 'https://')) {
            URL::forceRootUrl($appUrl);
            URL::forceScheme('https');
        }

        Route::bind('role', function ($value) {
            return Role::where('guard_name', config('auth.defaults.guard'))->findOrFail($value);
        });
    }
}
