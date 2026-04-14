<?php

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Core routes (health check endpoint)
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        // Core admin views
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'core');
    }
}
