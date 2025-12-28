<?php

declare(strict_types=1);

namespace AppModules\Payment\src\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \AppModules\Payment\src\Contracts\PaymentRepositoryContract::class,
            \AppModules\Payment\src\Repositories\PaymentRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes with web middleware
        if (file_exists($routesPath = __DIR__.'/../../routes/web.php')) {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group($routesPath);
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Load views with namespace
        $this->loadViewsFrom(resource_path('views/modules/payment'), 'payment');

        // Register anonymous Blade components
        Blade::anonymousComponentPath(
            resource_path('views/components/payment'),
            'payment'
        );
    }
}
