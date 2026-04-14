{{-- Modules/Announcements/Resources/views/index.blade.php --}}
@extends('layouts.app')   {{-- swap for your actual front-end layout --}}

@section('title', 'News & Announcements')

@section('content')
<div class="container py-8">
    <h1 class="text-2xl font-bold mb-6">News &amp; Announcements</h1>

    @forelse($announcements as $item)
        <article class="mb-6 p-5 border rounded-lg {{ $item->pinned ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200' }}">
            @if($item->pinned)
                <span class="text-xs font-semibold text-yellow-700 bg-yellow-200 px-2 py-1 rounded-full mb-2 inline-block">📌 Pinned</span>
            @endif

            <h2 class="text-xl font-semibold">
                <a href="{{ route('announcements.show', $item->slug) }}" class="hover:underline">
                    {{ $item->title }}
                </a>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ \Carbon\Carbon::parse($item->published_at)->format('j F Y') }}
            </p>
            <div class="mt-3 text-gray-700 prose">
                {!! Str::limit(strip_tags($item->body), 200) !!}
            </div>
            <a href="{{ route('announcements.show', $item->slug) }}" class="text-blue-600 text-sm mt-3 inline-block hover:underline">
                Read more →
            </a>
        </article>
    @empty
        <p class="text-gray-500">No announcements yet.</p>
    @endforelse

    {{ $announcements->links() }}
</div>
@endsection
