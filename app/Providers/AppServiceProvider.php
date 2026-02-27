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
        // Admin users have all permissions through Gate::before
        // This ensures admin role still works while allowing granular permissions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
            return null;
        });

        // Manually register policies to ensure Filament detects them
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Order::class, \App\Policies\OrderPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Payment::class, \App\Policies\PaymentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Product::class, \App\Policies\ProductPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\BranchInventory::class, \App\Policies\BranchInventoryPolicy::class);

        // Register Restrictive Policy for Admin-only resources
        $adminOnlyModels = [
            \App\Models\Banner::class,
            \App\Models\Branch::class,
            \App\Models\Category::class,
            \App\Models\GlobalConfig::class,
            \App\Models\Page::class,
            \App\Models\Sku::class,
            \App\Models\Testimonial::class,
            \App\Models\User::class,
            \App\Models\Voucher::class,
        ];

        foreach ($adminOnlyModels as $model) {
            \Illuminate\Support\Facades\Gate::policy($model, \App\Policies\GeneralAdminPolicy::class);
        }

        // Register observers
        \App\Models\Branch::observe(\App\Observers\BranchObserver::class);
        \App\Models\Sku::observe(\App\Observers\SkuObserver::class);
        \App\Models\BranchInventory::observe(\App\Observers\BranchInventoryObserver::class);
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
        \App\Models\Product::observe(\App\Observers\ProductImageObserver::class);

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
        // Register event listeners
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\OrderPaid::class,
            \App\Listeners\UpgradeUserGrade::class
        );
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
