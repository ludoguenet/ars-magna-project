<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register custom autoloader for AppModules to handle case-sensitivity
        spl_autoload_register(function (string $class): void {
            // Only handle AppModules namespace
            if (strpos($class, 'AppModules\\') !== 0) {
                return;
            }

            // Convert namespace to file path
            $relativeClass = substr($class, strlen('AppModules\\'));
            $parts = explode('\\', $relativeClass);

            // First part is the module name - convert to lowercase for directory
            $moduleName = strtolower(array_shift($parts));
            $file = base_path("app-modules/{$moduleName}/".implode('/', $parts).'.php');

            if (file_exists($file)) {
                require_once $file;
            }
        }, true, true);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
