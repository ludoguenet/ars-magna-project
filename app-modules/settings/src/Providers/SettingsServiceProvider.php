<?php

namespace AppModules\Settings\src\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register any bindings here
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
        $this->loadViewsFrom(resource_path('views/modules/settings'), 'settings');

        // Register anonymous Blade components
        Blade::anonymousComponentPath(
            resource_path('views/components/settings'),
            'settings'
        );
    }
}
