{{-- Modules/Announcements/Resources/views/admin/form.blade.php --}}
@extends('layouts.admin')

@section('title', isset($announcement) ? 'Edit Announcement' : 'New Announcement')

@section('content')
<div class="container py-8 max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">
        {{ isset($announcement) ? 'Edit Announcement' : 'New Announcement' }}
    </h1>

    <form action="{{ isset($announcement) ? route('admin.announcements.update', $announcement->id) : route('admin.announcements.store') }}"
          method="POST" class="space-y-5">
        @csrf
        @if(isset($announcement)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title"
                   value="{{ old('title', $announcement->title ?? '') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                   required>
            @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Body</label>
            <textarea name="body" rows="10"
                      class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                      required>{{ old('body', $announcement->body ?? '') }}</textarea>
            @error('body') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="published" value="1"
                       {{ old('published', $announcement->published ?? false) ? 'checked' : '' }}>
                <span class="text-sm">Published</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="pinned" value="1"
                       {{ old('pinned', $announcement->pinned ?? false) ? 'checked' : '' }}>
                <span class="text-sm">📌 Pin to top</span>
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
                {{ isset($announcement) ? 'Save Changes' : 'Create Announcement' }}
            </button>
            <a href="{{ route('admin.announcements.index') }}"
               class="px-5 py-2 rounded border border-gray-300 hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
