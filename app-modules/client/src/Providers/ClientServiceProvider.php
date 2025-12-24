<?php

namespace AppModules\Client\src\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \AppModules\Client\src\Contracts\ClientRepositoryContract::class,
            \AppModules\Client\src\Repositories\ClientRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes
        if (file_exists($routesPath = __DIR__.'/../../routes/web.php')) {
            $this->loadRoutesFrom($routesPath);
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Load views with namespace
        $this->loadViewsFrom(resource_path('views/modules/client'), 'client');

        // Register anonymous Blade components
        Blade::anonymousComponentPath(
            resource_path('views/components/client'),
            'client'
        );
    }
}
