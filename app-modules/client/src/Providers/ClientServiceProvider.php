<?php

declare(strict_types=1);

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
        // Load routes with web middleware
        if (file_exists($routesPath = __DIR__.'/../../routes/web.php')) {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group($routesPath);
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Load views with namespace from module directory
        if (is_dir(__DIR__.'/../../resources/views')) {
            $this->loadViewsFrom(__DIR__.'/../../resources/views', 'client');
        }

        // Register anonymous Blade components (if they exist in main resources)
        if (is_dir(resource_path('views/components/client'))) {
            Blade::anonymousComponentPath(
                resource_path('views/components/client'),
                'client'
            );
        }
    }
}
