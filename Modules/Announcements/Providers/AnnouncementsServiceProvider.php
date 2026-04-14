<?php

namespace Modules\Announcements\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AnnouncementsServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Register this module's routes
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        // Register Blade views under the 'announcements::' namespace
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'announcements');

        // Register migrations (run when module is enabled)
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Share a nav link with the layout so it appears in the site menu.
        // Your layout should use @stack('module_nav') or similar.
        View::share('moduleNavLinks', array_merge(
            View::shared('moduleNavLinks', []),
            [['label' => 'News', 'route' => 'announcements.index']]
        ));
    }
}
