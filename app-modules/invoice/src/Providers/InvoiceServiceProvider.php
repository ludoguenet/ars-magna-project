<?php

namespace AppModules\Invoice\src\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \AppModules\Invoice\src\Contracts\InvoiceRepositoryContract::class,
            \AppModules\Invoice\src\Repositories\InvoiceRepository::class
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
        $this->loadViewsFrom(resource_path('views/modules/invoice'), 'invoice');

        // Register anonymous Blade components
        Blade::anonymousComponentPath(
            resource_path('views/components/invoice'),
            'invoice'
        );
    }
}
