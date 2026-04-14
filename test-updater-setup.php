<?php
/**
 * RAYNET Module Updater Test Setup
 * Run this once: php test-updater-setup.php
 * It creates a TestWidget module and a fake update server route.
 */

$base = getcwd(); // run from public_html

// ── 1. Create TestWidget module ──────────────────────────────────────────
$modPath = $base . '/Modules/TestWidget';
@mkdir($modPath, 0755, true);
@mkdir($modPath . '/Providers', 0755, true);
@mkdir($modPath . '/Http/Controllers', 0755, true);
@mkdir($modPath . '/Resources/views', 0755, true);

// module.json — deliberately old version
file_put_contents($modPath . '/module.json', json_encode([
    'name'        => 'TestWidget',
    'slug'        => 'test-widget',
    'version'     => '1.0.0',
    'description' => 'A test module for verifying the update system.',
    'author'      => 'RAYNET Liverpool',
    'is_core'     => false,
    'can_disable' => true,
    'can_delete'  => true,
    'components'  => ['Test Widget'],
    'update_url'  => env('APP_URL') . '/admin/test-update-server',
], JSON_PRETTY_PRINT));

// ServiceProvider
file_put_contents($modPath . '/Providers/TestWidgetServiceProvider.php', <<<'PHP'
<?php
namespace Modules\TestWidget\Providers;
use Illuminate\Support\ServiceProvider;
class TestWidgetServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'test-widget');
    }
}
PHP);

// A simple view
file_put_contents($modPath . '/Resources/views/widget.blade.php', <<<'BLADE'
<div style="padding:1rem;background:#eef7f2;border-left:4px solid #1a6b3c;font-family:Arial,sans-serif">
    <strong>TestWidget v1.0.0</strong> — Update system test module. If you can see this, the module loaded correctly.
</div>
BLADE);

echo "✓ TestWidget module created at Modules/TestWidget/\n";

// ── 2. Add fake update server route to routes/web.php ───────────────────
$routesFile = $base . '/routes/web.php';
$routes     = file_get_contents($routesFile);

$fakeServerRoute = <<<'ROUTE'

/*
|--------------------------------------------------------------------------
| FAKE MODULE UPDATE SERVER — for testing only, remove in production
|--------------------------------------------------------------------------
*/
Route::get('/admin/test-update-server', function () {
    // Simulates a central RAYNET update server response
    return response()->json([
        'modules' => [
            [
                'slug'         => 'test-widget',
                'name'         => 'TestWidget',
                'version'      => '2.0.0',   // newer than installed 1.0.0
                'description'  => 'A test module for verifying the update system.',
                'changelog'    => "v2.0.0\n- Added widget refresh button\n- Improved styling\n- Fixed compatibility with PHP 8.2",
                'download_url' => url('/admin/test-update-download'),
                'checksum'     => '',   // left blank for test
                'requires_php' => '8.1',
                'released_at'  => now()->toDateString(),
            ],
        ],
    ]);
})->middleware(['web', 'admin']);

Route::get('/admin/test-update-download', function () {
    // Serves a zip of the updated TestWidget module
    $tmpDir = sys_get_temp_dir() . '/TestWidget';
    @mkdir($tmpDir . '/Providers', 0755, true);
    @mkdir($tmpDir . '/Resources/views', 0755, true);

    file_put_contents($tmpDir . '/module.json', json_encode([
        'name'        => 'TestWidget',
        'slug'        => 'test-widget',
        'version'     => '2.0.0',
        'description' => 'A test module for verifying the update system.',
        'author'      => 'RAYNET Liverpool',
        'is_core'     => false,
        'can_disable' => true,
        'can_delete'  => true,
        'components'  => ['Test Widget', 'Refresh Button'],
        'update_url'  => url('/admin/test-update-server'),
    ], JSON_PRETTY_PRINT));

    file_put_contents($tmpDir . '/Providers/TestWidgetServiceProvider.php', '<?php
namespace Modules\TestWidget\Providers;
use Illuminate\Support\ServiceProvider;
class TestWidgetServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . "/../Resources/views", "test-widget");
    }
}');

    file_put_contents($tmpDir . '/Resources/views/widget.blade.php',
        '<div style="padding:1rem;background:#003366;color:#fff;font-family:Arial,sans-serif;border-left:4px solid #C8102E">'
        . '<strong>TestWidget v2.0.0</strong> — Successfully updated! The refresh button and improved styling are now active.'
        . '</div>');

    // Create zip
    $zipPath = sys_get_temp_dir() . '/TestWidget-2.0.0.zip';
    if (file_exists($zipPath)) unlink($zipPath);

    $zip = new ZipArchive();
    $zip->open($zipPath, ZipArchive::CREATE);
    $zip->addFile($tmpDir . '/module.json',                                    'TestWidget/module.json');
    $zip->addFile($tmpDir . '/Providers/TestWidgetServiceProvider.php',        'TestWidget/Providers/TestWidgetServiceProvider.php');
    $zip->addFile($tmpDir . '/Resources/views/widget.blade.php',               'TestWidget/Resources/views/widget.blade.php');
    $zip->close();

    return response()->download($zipPath, 'TestWidget-2.0.0.zip', [
        'Content-Type' => 'application/zip',
    ])->deleteFileAfterSend(true);
})->middleware(['web', 'admin']);

ROUTE;

if (!str_contains($routes, 'FAKE MODULE UPDATE SERVER')) {
    // Add before the auth require at the end
    $routes = str_replace(
        "require __DIR__ . '/auth.php';",
        $fakeServerRoute . "\nrequire __DIR__ . '/auth.php';",
        $routes
    );
    file_put_contents($routesFile, $routes);
    echo "✓ Fake update server routes added to routes/web.php\n";
} else {
    echo "ℹ Fake update server routes already present\n";
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "DONE. Next steps:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "1. php artisan route:clear && php artisan cache:clear\n";
echo "2. Go to Admin → Module Manager\n";
echo "3. Enable the TestWidget module\n";
echo "4. Click 'Check for Updates' — it should show v2.0.0 available\n";
echo "5. Click 'Update' — it should download and install v2.0.0\n";
echo "6. Verify module.json now shows version 2.0.0\n";
echo "\n";
echo "Test server URL: " . (getenv('APP_URL') ?: 'https://yoursite.com') . "/admin/test-update-server\n";
echo "Test download URL: " . (getenv('APP_URL') ?: 'https://yoursite.com') . "/admin/test-update-download\n";
