<?php

namespace Modules\NetLog\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class NetLogServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Log::info('NetLog provider booting');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        Log::info('NetLog routes loaded');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'netlog');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        Log::info('NetLog provider booted');
    }
}
