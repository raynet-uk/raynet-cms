<?php

namespace App\Providers;

use App\Services\ModuleManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/raynet_modules.php',
            'raynet_modules'
        );

        $this->app->singleton(ModuleManager::class, fn () => new ModuleManager());
        $this->app->alias(ModuleManager::class, 'module.manager');
    }

    public function boot(): void
    {
        // Boot modules after all other providers have registered
        // so routes, middleware and DB are all available
        $this->app->booted(function () {
            $this->app->make(ModuleManager::class)->boot();
        });
    }
}
