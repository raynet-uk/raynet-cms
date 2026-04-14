<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class CoreHealthController extends Controller
{
    public function index()
    {
        $checks = $this->runChecks();
        $manifest = json_decode(
            file_get_contents(base_path('Modules/Core/module.json')),
            true
        );

        return view('core::admin.health', compact('checks', 'manifest'));
    }

    protected function runChecks(): array
    {
        return [
            'php' => [
                'label'   => 'PHP Version',
                'value'   => PHP_VERSION,
                'status'  => version_compare(PHP_VERSION, '8.1.0', '>=') ? 'ok' : 'warn',
                'detail'  => version_compare(PHP_VERSION, '8.1.0', '>=')
                    ? 'PHP ' . PHP_VERSION . ' — meets minimum requirement'
                    : 'PHP 8.1+ recommended',
            ],
            'laravel' => [
                'label'   => 'Laravel Version',
                'value'   => app()->version(),
                'status'  => 'ok',
                'detail'  => 'Laravel ' . app()->version(),
            ],
            'database' => [
                'label'   => 'Database Connection',
                'value'   => $this->checkDatabase(),
                'status'  => $this->databaseOk() ? 'ok' : 'error',
                'detail'  => $this->databaseOk()
                    ? 'Connected to ' . DB::connection()->getDatabaseName()
                    : 'Cannot connect to database',
            ],
            'cache' => [
                'label'   => 'Cache',
                'value'   => $this->checkCache(),
                'status'  => $this->cacheOk() ? 'ok' : 'warn',
                'detail'  => $this->cacheOk() ? 'Cache read/write working' : 'Cache may not be functioning',
            ],
            'storage_writable' => [
                'label'   => 'Storage Writable',
                'value'   => is_writable(storage_path()) ? 'Writable' : 'Not writable',
                'status'  => is_writable(storage_path()) ? 'ok' : 'error',
                'detail'  => storage_path(),
            ],
            'env' => [
                'label'   => 'Environment',
                'value'   => config('app.env'),
                'status'  => config('app.env') === 'production' ? 'ok' : 'warn',
                'detail'  => config('app.env') === 'production'
                    ? 'Running in production mode'
                    : 'Not in production — debug may be enabled',
            ],
            'debug' => [
                'label'   => 'Debug Mode',
                'value'   => config('app.debug') ? 'ON' : 'OFF',
                'status'  => config('app.debug') ? 'warn' : 'ok',
                'detail'  => config('app.debug')
                    ? 'Debug mode is ON — disable in production'
                    : 'Debug mode is off',
            ],
            'queue' => [
                'label'   => 'Queue Driver',
                'value'   => config('queue.default'),
                'status'  => config('queue.default') !== 'sync' ? 'ok' : 'warn',
                'detail'  => config('queue.default') === 'sync'
                    ? 'Using sync driver — emails send inline (may be slow)'
                    : 'Using async queue driver',
            ],
            'modules_path' => [
                'label'   => 'Modules Directory',
                'value'   => is_dir(base_path('Modules')) ? 'Exists' : 'Missing',
                'status'  => is_dir(base_path('Modules')) ? 'ok' : 'error',
                'detail'  => base_path('Modules'),
            ],
        ];
    }

    protected function databaseOk(): bool
    {
        try { DB::connection()->getPdo(); return true; } catch (\Throwable $e) { return false; }
    }

    protected function checkDatabase(): string
    {
        try { DB::connection()->getPdo(); return 'Connected'; } catch (\Throwable $e) { return 'Failed'; }
    }

    protected function cacheOk(): bool
    {
        try {
            Cache::put('core_health_test', 'ok', 5);
            return Cache::get('core_health_test') === 'ok';
        } catch (\Throwable $e) { return false; }
    }

    protected function checkCache(): string
    {
        return $this->cacheOk() ? 'Working' : 'Failed';
    }
}
