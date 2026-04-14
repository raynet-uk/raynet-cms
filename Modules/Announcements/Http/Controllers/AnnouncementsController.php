<?php

namespace Modules\Announcements\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnnouncementsController extends Controller
{
    // ── Public ────────────────────────────────────────────────────────────

    public function index()
    {
        $announcements = DB::table('announcements')
            ->where('published', true)
            ->orderByDesc('pinned')
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('announcements::index', compact('announcements'));
    }

    public function show(string $slug)
    {
        $announcement = DB::table('announcements')
            ->where('slug', $slug)
            ->where('published', true)
            ->firstOrFail();

        return view('announcements::show', compact('announcement'));
    }

    // ── Admin ─────────────────────────────────────────────────────────────

    public function adminIndex()
    {
        $announcements = DB::table('announcements')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('announcements::admin.index', compact('announcements'));
    }

    public function create()
    {
        return view('announcements::admin.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'body'    => 'required|string',
            'pinned'  => 'boolean',
            'published' => 'boolean',
        ]);

        DB::table('announcements')->insert([
            'title'        => $data['title'],
            'slug'         => Str::slug($data['title']) . '-' . time(),
            'body'         => $data['body'],
            'pinned'       => $request->boolean('pinned'),
            'published'    => $request->boolean('published'),
            'published_at' => $request->boolean('published') ? now() : null,
            'author_id'    => auth()->id(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->route('admin.announcements.index')
                         ->with('success', 'Announcement created.');
    }

    public function edit(int $id)
    {
        $announcement = DB::table('announcements')->findOrFail($id);
        return view('announcements::admin.form', compact('announcement'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required|string',
            'pinned'    => 'boolean',
            'published' => 'boolean',
        ]);

        DB::table('announcements')->where('id', $id)->update([
            'title'        => $data['title'],
            'body'         => $data['body'],
            'pinned'       => $request->boolean('pinned'),
            'published'    => $request->boolean('published'),
            'published_at' => $request->boolean('published') ? now() : null,
            'updated_at'   => now(),
        ]);

        return redirect()->route('admin.announcements.index')
                         ->with('success', 'Announcement updated.');
    }

    public function destroy(int $id)
    {
        DB::table('announcements')->where('id', $id)->delete();
        return redirect()->route('admin.announcements.index')
                         ->with('success', 'Announcement deleted.');
    }
}
