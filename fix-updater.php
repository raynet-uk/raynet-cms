<?php
/**
 * RAYNET Module Updater Fix
 * Run from public_html: php fix-updater.php
 */

$base = getcwd();

// ── 1. Fix TestWidget module.json ─────────────────────────────────────────
@mkdir($base . '/Modules/TestWidget/Providers', 0755, true);
@mkdir($base . '/Modules/TestWidget/Resources/views', 0755, true);

file_put_contents($base . '/Modules/TestWidget/module.json', json_encode([
    'name'        => 'TestWidget',
    'alias'       => 'test-widget',
    'version'     => '1.0.0',
    'description' => 'A test module for verifying the update system.',
    'author'      => 'RAYNET Liverpool',
    'is_core'     => false,
    'can_disable' => true,
    'can_delete'  => true,
    'components'  => ['Test Widget'],
    'providers'   => [],
], JSON_PRETTY_PRINT));

file_put_contents($base . '/Modules/TestWidget/Providers/TestWidgetServiceProvider.php',
'<?php
namespace Modules\TestWidget\Providers;
use Illuminate\Support\ServiceProvider;
class TestWidgetServiceProvider extends ServiceProvider {
    public function boot(): void {
        $this->loadViewsFrom(__DIR__ . "/../Resources/views", "test-widget");
    }
}');

file_put_contents($base . '/Modules/TestWidget/Resources/views/widget.blade.php',
'<div style="padding:1rem;background:#eef7f2;border-left:4px solid #1a6b3c;font-family:Arial,sans-serif">
    <strong>TestWidget v1.0.0</strong> — Update test module. Waiting to be updated to v2.0.0.
</div>');

echo "✓ TestWidget 1.0.0 created\n";

// ── 2. Get APP_URL from .env ──────────────────────────────────────────────
$env = file_get_contents($base . '/.env');
preg_match('/^APP_URL=(.+)$/m', $env, $m);
$appUrl = trim($m[1] ?? 'https://raynet-liverpool.net', '"\'');
echo "✓ APP_URL: $appUrl\n";

// ── 3. Fix config/raynet_modules.php ─────────────────────────────────────
file_put_contents($base . '/config/raynet_modules.php',
'<?php
return [
    \'update_server_url\' => \'' . $appUrl . '/admin/test-update-server\',
];
');
echo "✓ config/raynet_modules.php updated\n";

// ── 4. Replace/add fake server routes in routes/web.php ──────────────────
$routesFile = $base . '/routes/web.php';
$routes = file_get_contents($routesFile);

// Remove old fake server block if exists
$routes = preg_replace(
    '/\/\*\s*\|[-]+\s*\|\s*FAKE MODULE UPDATE SERVER.*?(?=\/\*|\z)/s',
    '',
    $routes
);

$v2Json = json_encode([
    'name'        => 'TestWidget',
    'alias'       => 'test-widget',
    'version'     => '2.0.0',
    'description' => 'A test module for verifying the update system.',
    'author'      => 'RAYNET Liverpool',
    'is_core'     => false,
    'can_disable' => true,
    'can_delete'  => true,
    'components'  => ['Test Widget', 'Refresh Button'],
    'providers'   => [],
], JSON_PRETTY_PRINT);

$v2View = '<div style=\"padding:1rem;background:#003366;color:#fff;border-left:4px solid #C8102E;font-family:Arial,sans-serif\">'
        . '<strong>TestWidget v2.0.0<\/strong> — Updated successfully! Refresh button &amp; improved styling active.'
        . '<\/div>';

$fakeRoutes = <<<ROUTES

/*
|--------------------------------------------------------------------------
| FAKE MODULE UPDATE SERVER — testing only, remove when done
|--------------------------------------------------------------------------
*/
Route::get('/admin/test-update-server', function () {
    return response()->json([
        'modules' => [
            'test-widget' => [
                'version'      => '2.0.0',
                'name'         => 'TestWidget',
                'description'  => 'A test module for verifying the update system.',
                'download_url' => url('/admin/test-update-download'),
                'changelog'    => "v2.0.0\n- Added widget refresh button\n- Improved styling\n- Fixed PHP 8.2 compat",
                'requires_php' => '8.1',
                'released_at'  => now()->toDateString(),
            ],
        ],
    ]);
})->middleware(['web', 'admin']);

Route::get('/admin/test-update-download', function () {
    $tmp = sys_get_temp_dir() . '/TestWidget_v2';
    @mkdir($tmp . '/Providers', 0755, true);
    @mkdir($tmp . '/Resources/views', 0755, true);

    file_put_contents($tmp . '/module.json', '{$v2Json}');

    file_put_contents($tmp . '/Providers/TestWidgetServiceProvider.php', '<?php
namespace Modules\TestWidget\Providers;
use Illuminate\Support\ServiceProvider;
class TestWidgetServiceProvider extends ServiceProvider {
    public function boot(): void {
        $this->loadViewsFrom(__DIR__ . "/../Resources/views", "test-widget");
    }
}');

    file_put_contents($tmp . '/Resources/views/widget.blade.php',
        '{$v2View}');

    $zipPath = sys_get_temp_dir() . '/TestWidget-2.0.0.zip';
    if (file_exists($zipPath)) unlink($zipPath);
    $zip = new ZipArchive();
    $zip->open($zipPath, ZipArchive::CREATE);
    $zip->addFile($tmp . '/module.json',                             'TestWidget/module.json');
    $zip->addFile($tmp . '/Providers/TestWidgetServiceProvider.php', 'TestWidget/Providers/TestWidgetServiceProvider.php');
    $zip->addFile($tmp . '/Resources/views/widget.blade.php',        'TestWidget/Resources/views/widget.blade.php');
    $zip->close();

    return response()->download($zipPath, 'TestWidget-2.0.0.zip', [
        'Content-Type' => 'application/zip',
    ])->deleteFileAfterSend(true);
})->middleware(['web', 'admin']);

ROUTES;

$routes = str_replace(
    "require __DIR__ . '/auth.php';",
    $fakeRoutes . "require __DIR__ . '/auth.php';",
    $routes
);
file_put_contents($routesFile, $routes);
echo "✓ Fake update server routes added\n";

// ── 5. Make sure ModuleController has update action ───────────────────────
$controllerPath = $base . '/app/Http/Controllers/Admin/ModuleController.php';
if (file_exists($controllerPath)) {
    $mc = file_get_contents($controllerPath);
    if (!str_contains($mc, 'function checkUpdates') && !str_contains($mc, 'checkUpdates')) {
        // Add before last closing brace
        $newMethods = '
    public function checkUpdates()
    {
        Cache::forget(\'raynet_module_updates\');
        $updates = app(\App\Services\ModuleManager::class)->checkUpdates();
        return redirect()->route(\'admin.modules.index\')
            ->with(\'updates\', $updates)
            ->with(\'success\', count($updates) > 0
                ? count($updates) . \' update(s) available.\'
                : \'All modules are up to date.\');
    }

    public function applyUpdate(string $alias)
    {
        try {
            app(\App\Services\ModuleManager::class)->update($alias);
            Cache::forget(\'raynet_module_updates\');
            Cache::forget(\'raynet_modules_enabled\');
            return redirect()->route(\'admin.modules.index\')
                ->with(\'success\', "Module \'{$alias}\' updated successfully.");
        } catch (\Exception $e) {
            return redirect()->route(\'admin.modules.index\')
                ->withErrors([\'update\' => $e->getMessage()]);
        }
    }
';
        $mc = substr($mc, 0, strrpos($mc, '}')) . $newMethods . '}';
        // Add Cache use if missing
        if (!str_contains($mc, 'use Illuminate\Support\Facades\Cache;')) {
            $mc = str_replace(
                'use Illuminate\Support\Facades\File;',
                "use Illuminate\Support\Facades\File;\nuse Illuminate\Support\Facades\Cache;",
                $mc
            );
        }
        file_put_contents($controllerPath, $mc);
        echo "✓ ModuleController updated with checkUpdates/applyUpdate\n";
    } else {
        echo "ℹ ModuleController already has update methods\n";
    }
} else {
    echo "✗ ModuleController not found at $controllerPath\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Now run:\n";
echo "  php artisan route:clear && php artisan config:clear && php artisan cache:clear\n\n";
echo "Then add these two routes to routes/modules.php:\n";
echo "  Route::get('updates/check',          [ModuleController::class, 'checkUpdates'])->name('modules.updates.check');\n";
echo "  Route::post('{alias}/update/apply',  [ModuleController::class, 'applyUpdate'])->name('modules.update.apply');\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
