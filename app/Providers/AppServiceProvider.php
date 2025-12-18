<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production and when using ngrok (ngrok always uses HTTPS)
        if ($this->app->environment('production') || $this->isNgrokUrl()) {
            URL::forceScheme('https');
        }
    }

    /**
     * Check if current URL is from ngrok
     */
    private function isNgrokUrl(): bool
    {
        $host = request()->getHost();

        return str_contains($host, 'ngrok');
    }
}
