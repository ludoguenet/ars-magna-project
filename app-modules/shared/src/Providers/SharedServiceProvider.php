<?php

namespace AppModules\Shared\src\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SharedServiceProvider extends ServiceProvider
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
        // Register anonymous Blade components
        Blade::anonymousComponentPath(
            resource_path('views/components/shared'),
            'shared'
        );
    }
}
