<?php

namespace AppModules\Notification\src\Providers;

use AppModules\Notification\src\Contracts\NotificationRepositoryContract;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \AppModules\Notification\src\Contracts\NotificationRepositoryContract::class,
            \AppModules\Notification\src\Repositories\NotificationRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes with web middleware
        if (file_exists($routesPath = __DIR__.'/../../routes/web.php')) {
            Route::middleware('web')->group($routesPath);
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Load views with namespace from module directory
        if (is_dir(__DIR__.'/../../resources/views')) {
            $this->loadViewsFrom(__DIR__.'/../../resources/views', 'notification');
        }

        // Register anonymous Blade components (if they exist in main resources)
        if (is_dir(resource_path('views/components/notification'))) {
            Blade::anonymousComponentPath(
                resource_path('views/components/notification'),
                'notification'
            );
        }

        // Share notification data with all views (for navigation badge)
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $repository = app(NotificationRepositoryContract::class);
                $notifications = $repository->getUnreadForUser(auth()->id());
                $unreadCount = $repository->getUnreadCount(auth()->id());

                $view->with([
                    'unreadNotifications' => $notifications,
                    'unreadNotificationCount' => $unreadCount,
                ]);
            }
        });
    }
}
