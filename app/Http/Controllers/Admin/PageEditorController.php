<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;

class PageEditorController extends Controller
{
    protected string $pagesPath;
    protected string $backupsPath;

    // Pages that have registered routes — used for the "View Page" link
    protected array $routeMap = [
        'home'            => '/',
        'about'           => '/about',
        'event-support'   => '/event-support',
        'training'        => '/training',
        'cookies'         => '/cookies',
        'privacy'         => '/privacy',
        'request-support' => '/request-support',
        'calendar'        => '/calendar',
        'members'         => '/members',
        'ops-map'         => '/ops-map',
        'profile'         => '/profile',
    ];

    // Pages with complex PHP logic — only source edit, no visual mode
    protected array $complexPages = [
        'ops-map', 'members', 'calendar', 'profile',
    ];

    public function __construct()
    {
        $this->pagesPath  = resource_path('views/pages');
        $this->backupsPath = storage_path('app/page-backups');
    }

    public function index()
    {
        $files = collect(File::files($this->pagesPath))
            ->filter(fn($f) => str_ends_with($f->getFilename(), '.blade.php'))
            ->map(fn($f) => [
                'slug'     => str_replace('.blade.php', '', $f->getFilename()),
                'filename' => $f->getFilename(),
                'size'     => $this->humanSize($f->getSize()),
                'bytes'    => $f->getSize(),
                'modified' => Carbon::createFromTimestamp($f->getMTime()),
                'path'     => $f->getPathname(),
            ])
            ->sortBy('slug')
            ->values();

        // Count backups per page
        $backupCounts = [];
        if (File::isDirectory($this->backupsPath)) {
            foreach (File::files($this->backupsPath) as $bf) {
                preg_match('/^([a-z0-9\-]+)_/', $bf->getFilename(), $m);
                if (isset($m[1])) {
                    $backupCounts[$m[1]] = ($backupCounts[$m[1]] ?? 0) + 1;
                }
            }
        }

        return view('admin.pages.index', [
            'files'        => $files,
            'routeMap'     => $this->routeMap,
            'complexPages' => $this->complexPages,
            'backupCounts' => $backupCounts,
        ]);
    }

    public function edit(string $slug)
    {
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $path = $this->pagesPath . '/' . $slug . '.blade.php';
        abort_unless(File::exists($path), 404, 'Page not found.');

        $raw      = File::get($path);
        $modified = Carbon::createFromTimestamp(File::lastModified($path));
        $size     = $this->humanSize(File::size($path));
        $isComplex = in_array($slug, $this->complexPages);
        $url      = $this->routeMap[$slug] ?? null;

        // Extract the @section('content') block for visual mode
        $visualContent = null;
        if (!$isComplex) {
            $visualContent = $this->extractContentSection($raw);
        }

        // Backups for this page
        $backups = $this->getBackups($slug);

        return view('admin.pages.edit', [
            'slug'             => $slug,
            'raw'              => $raw,
            'modified'         => $modified,
            'size'             => $size,
            'isComplex'        => $isComplex,
            'url'              => $url,
            'visualContent'    => $visualContent,   // kept for source mode reference
            'visualContentB64' => $visualContent !== null ? base64_encode($visualContent) : null,
            'backups'          => $backups,
        ]);
    }

    public function update(Request $request, string $slug)
    {
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $path = $this->pagesPath . '/' . $slug . '.blade.php';
        abort_unless(File::exists($path), 404);

        $request->validate([
            'content' => ['required', 'string'],
            'mode'    => ['required', 'in:source,visual'],
        ]);

        // Backup first
        $this->createBackup($slug, $path);

        if ($request->mode === 'visual') {
            // Replace just the content section in the existing file
            $original = File::get($path);
            $newContent = $this->replaceContentSection($original, $request->content);
            File::put($path, $newContent);
        } else {
            File::put($path, $request->content);
        }

        Artisan::call('view:clear');

        return redirect()->route('admin.pages.edit', $slug)
            ->with('success', 'Page saved. Backup created.');
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug'  => ['required', 'string', 'max:50', 'regex:/^[a-z0-9\-]+$/'],
            'title' => ['required', 'string', 'max:100'],
        ]);

        $slug = $request->slug;
        $path = $this->pagesPath . '/' . $slug . '.blade.php';

        if (File::exists($path)) {
            return back()->withErrors(['slug' => 'A page with this slug already exists.'])->withInput();
        }

        File::put($path, $this->newPageTemplate($request->title, $slug));

        // Optionally add a public route immediately
        if ($request->boolean('create_route')) {
            $url = '/' . ltrim(preg_replace('/[^a-z0-9\-\/]/', '', $request->input('url', $slug)), '/');
            $this->updatePageRoute(null, $slug, $url);
            Artisan::call('route:clear');
            return redirect()->route('admin.pages.edit', $slug)
                ->with('success', "Page '{$slug}' created and published at <code>{$url}</code>.");
        }

        return redirect()->route('admin.pages.edit', $slug)
            ->with('success', "Page '{$slug}' created. Use the URL Settings button to publish it.");
    }

    public function restoreBackup(Request $request, string $slug)
    {
        $slug     = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $filename = basename($request->input('backup'));
        $backupPath = $this->backupsPath . '/' . $filename;
        $livePath   = $this->pagesPath . '/' . $slug . '.blade.php';

        abort_unless(
            File::exists($backupPath) && str_starts_with($filename, $slug . '_'),
            404, 'Invalid backup.'
        );

        // Backup the current before restoring
        $this->createBackup($slug, $livePath);
        File::copy($backupPath, $livePath);
        Artisan::call('view:clear');

        return redirect()->route('admin.pages.edit', $slug)
            ->with('success', 'Backup restored. Current version saved as a backup.');
    }

    public function backups(string $slug)
    {
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        return response()->json($this->getBackups($slug));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    protected function extractContentSection(string $blade): ?string
    {
        // Match @section('content') ... @endsection
        if (preg_match("/@section\(['\"]content['\"]\)(.*?)@endsection/s", $blade, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    protected function replaceContentSection(string $blade, string $newHtml): string
    {
        return preg_replace(
            "/(@section\(['\"]content['\"]\))(.*?)(@endsection)/s",
            '$1' . "\n" . $newHtml . "\n" . '$3',
            $blade
        );
    }

    protected function createBackup(string $slug, string $path): void
    {
        if (!File::isDirectory($this->backupsPath)) {
            File::makeDirectory($this->backupsPath, 0755, true);
        }
        $dest = $this->backupsPath . '/' . $slug . '_' . now()->format('Ymd_His') . '.blade.php';
        File::copy($path, $dest);

        // Keep only the 10 most recent backups per page
        $backups = $this->getBackups($slug);
        if (count($backups) > 10) {
            foreach (array_slice($backups, 10) as $old) {
                File::delete($this->backupsPath . '/' . $old['filename']);
            }
        }
    }

    protected function getBackups(string $slug): array
    {
        if (!File::isDirectory($this->backupsPath)) return [];

        return collect(File::files($this->backupsPath))
            ->filter(fn($f) => str_starts_with($f->getFilename(), $slug . '_'))
            ->map(fn($f) => [
                'filename' => $f->getFilename(),
                'size'     => $this->humanSize($f->getSize()),
                'date'     => Carbon::createFromTimestamp($f->getMTime())->format('d M Y H:i:s'),
                'ago'      => Carbon::createFromTimestamp($f->getMTime())->diffForHumans(),
            ])
            ->sortByDesc('filename')
            ->values()
            ->toArray();
    }

    protected function humanSize(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    protected function newPageTemplate(string $title, string $slug): string
    {
        return "@extends('layouts.app')
@section('title', '{$title}')
@section('content')

<style>
/* Page styles */
.page-wrap { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem 4rem; }
.page-hero { text-align: center; padding: 3rem 1rem 2rem; }
.page-hero h1 { font-size: 2rem; font-weight: bold; color: var(--navy); margin-bottom: 1rem; }
.page-hero p { font-size: 1.1rem; color: var(--text-muted); max-width: 600px; margin: 0 auto; }
</style>

<div class=\"page-wrap\">
    <div class=\"page-hero\">
        <h1>{$title}</h1>
        <p>Add your page description here.</p>
    </div>

    <div class=\"page-content\">
        <p>Start adding your content here. You can edit this page from the admin panel.</p>
    </div>
</div>

@endsection
";
    }
    // ── URL / Route management ──────────────────────────────────────────────

    public function rename(Request $request, string $slug)
    {
        $slug    = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $newSlug = preg_replace('/[^a-z0-9\-]/', '', $request->input('new_slug', ''));
        $newUrl  = '/' . ltrim(preg_replace('/[^a-z0-9\-\/]/', '', $request->input('new_url', '')), '/');

        $oldPath = $this->pagesPath . '/' . $slug . '.blade.php';
        $newPath = $this->pagesPath . '/' . $newSlug . '.blade.php';

        abort_unless(File::exists($oldPath), 404);

        if (empty($newSlug)) {
            return back()->withErrors(['new_slug' => 'Slug cannot be empty.']);
        }

        if ($newSlug !== $slug && File::exists($newPath)) {
            return back()->withErrors(['new_slug' => "A page with slug '{$newSlug}' already exists."]);
        }

        // Backup before doing anything
        $this->createBackup($slug, $oldPath);

        // Rename the blade file if slug changed
        if ($newSlug !== $slug) {
            File::move($oldPath, $newPath);

            // Move any builder block data
            $blocks = \App\Models\Setting::get('page_builder_blocks_' . $slug);
            if ($blocks) {
                \App\Models\Setting::set('page_builder_blocks_' . $newSlug, $blocks);
                \App\Models\Setting::where('key', 'page_builder_blocks_' . $slug)->delete();
            }
        }

        // Update or add the route in routes/web.php
        $result = $this->updatePageRoute($slug, $newSlug, $newUrl);

        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return redirect()->route('admin.pages.edit', $newSlug)
            ->with('success', "Page URL updated to <code>{$newUrl}</code>. " . ($result === 'added' ? 'Route added.' : ($result === 'updated' ? 'Route updated.' : 'Route file updated.')));
    }

    public function addRoute(Request $request, string $slug)
    {
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $url  = '/' . ltrim(preg_replace('/[^a-z0-9\-\/]/', '', $request->input('url', $slug)), '/');

        $result = $this->updatePageRoute(null, $slug, $url);

        Artisan::call('route:clear');

        return redirect()->route('admin.pages.edit', $slug)
            ->with('success', "Route added: <code>{$url}</code> → {$slug}.blade.php");
    }

    /**
     * Update or add a Route::view() entry in routes/web.php.
     * Returns 'updated', 'added', or 'already_exists'.
     */
    protected function updatePageRoute(?string $oldSlug, string $newSlug, string $newUrl): string
    {
        $routesPath = base_path('routes/web.php');
        $content    = File::get($routesPath);

        File::ensureDirectoryExists(storage_path('app/page-backups'));
        File::copy($routesPath, storage_path('app/page-backups/web_' . now()->format('Ymd_His') . '.php'));

        $newLine = "Route::view('" . $newUrl . "', 'pages." . $newSlug . "')->name('" . $newSlug . "');";

        if ($oldSlug) {
            $lines = explode("\n", $content);
            foreach ($lines as $i => $line) {
                if (str_contains($line, 'pages.' . $oldSlug) && str_contains($line, 'Route::view')) {
                    $lines[$i] = $newLine;
                    File::put($routesPath, implode("\n", $lines));
                    return 'updated';
                }
            }
            foreach ($lines as $i => $line) {
                if (str_contains($line, '/' . $oldSlug) && str_contains($line, 'Route::view')) {
                    $lines[$i] = $newLine;
                    File::put($routesPath, implode("\n", $lines));
                    return 'updated';
                }
            }
        }

        if (str_contains($content, 'pages.' . $newSlug)) {
            return 'already_exists';
        }

        $anchors = ["Route::view('/privacy',", "Route::view('/cookies',", "Route::view('/training',"];
        foreach ($anchors as $anchor) {
            $pos = strpos($content, $anchor);
            if ($pos !== false) {
                $lineEnd = strpos($content, "\n", $pos);
                $content = substr($content, 0, $lineEnd) . "\n" . $newLine . substr($content, $lineEnd);
                File::put($routesPath, $content);
                return 'added';
            }
        }

        $content = str_replace(
            "Route::get('/data-dashboard'",
            $newLine . "\n\nRoute::get('/data-dashboard'",
            $content
        );
        File::put($routesPath, $content);
        return 'added';
    }


}