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
        // Register observers
        // Register observers
        \App\Models\Branch::observe(\App\Observers\BranchObserver::class);

        // Share categories with footer component
        \Illuminate\Support\Facades\View::composer('components.footer', function ($view) {
            $view->with('categories', \App\Models\Category::where('is_active', true)
                ->whereNotNull('image')
                ->orderBy('sort_order')
                ->limit(6)
                ->get());
        });

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
