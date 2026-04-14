<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DataDashboardController extends Controller
{
    public function __invoke()
    {
        // Look for Markdown briefs in storage/app/condx
        $files = collect(Storage::files('condx'))
            ->filter(fn ($path) => str_ends_with($path, '.md'))
            ->sort()          // oldest → newest
            ->values();

        // Prefer explicit latest.md, fall back to newest dated file
        $latestPath = null;

        if (Storage::exists('condx/latest.md')) {
            $latestPath = 'condx/latest.md';
        } elseif ($files->isNotEmpty()) {
            $latestPath = $files->last();
        }

        if ($latestPath) {
            $markdown = Storage::get($latestPath);
        } else {
            // Failsafe message if nothing has been generated yet
            $markdown = <<<MD
# UK Propagation Brief

_No brief has been generated yet. The scheduler has probably not run `condx:generate`._
MD;
        }

        // Convert Markdown → HTML (uses Str::markdown, which relies on league/commonmark)
        $html = Str::markdown($markdown);

        return view('data-dashboard', [
            'condxHtml' => $html,
        ]);
    }
}