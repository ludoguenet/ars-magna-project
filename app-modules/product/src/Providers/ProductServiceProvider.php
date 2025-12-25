<?php

declare(strict_types=1);

namespace AppModules\Product\src\Providers;

use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \AppModules\Product\src\Contracts\ProductRepositoryContract::class,
            \AppModules\Product\src\Repositories\ProductRepository::class
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
            $this->loadViewsFrom(__DIR__.'/../../resources/views', 'product');
        }
    }
}
