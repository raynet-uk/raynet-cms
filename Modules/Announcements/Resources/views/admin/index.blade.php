{{-- Modules/Announcements/Resources/views/admin/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manage Announcements')

@section('content')
<div class="container py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Announcements</h1>
        <a href="{{ route('admin.announcements.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + New Announcement
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <table class="w-full border-collapse text-sm">
        <thead>
            <tr class="bg-gray-50 text-left">
                <th class="px-4 py-2 border">Title</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Pinned</th>
                <th class="px-4 py-2 border">Published</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($announcements as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border font-medium">{{ $item->title }}</td>
                <td class="px-4 py-2 border">
                    @if($item->published)
                        <span class="text-green-700 font-semibold">Published</span>
                    @else
                        <span class="text-gray-500">Draft</span>
                    @endif
                </td>
                <td class="px-4 py-2 border">{{ $item->pinned ? '📌' : '—' }}</td>
                <td class="px-4 py-2 border text-gray-500 text-xs">
                    {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('d/m/Y') : '—' }}
                </td>
                <td class="px-4 py-2 border space-x-2">
                    <a href="{{ route('admin.announcements.edit', $item->id) }}"
                       class="text-blue-600 hover:underline">Edit</a>
                    <form action="{{ route('admin.announcements.destroy', $item->id) }}"
                          method="POST" class="inline"
                          onsubmit="return confirm('Delete this announcement?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center py-8 text-gray-400">No announcements yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $announcements->links() }}</div>
</div>
@endsection
