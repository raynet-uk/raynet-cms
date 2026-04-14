{{-- Modules/Announcements/Resources/views/show.blade.php --}}
@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="container py-8 max-w-2xl">
    <a href="{{ route('announcements.index') }}" class="text-blue-600 text-sm hover:underline">← Back to News</a>

    <article class="mt-6">
        <h1 class="text-3xl font-bold">{{ $announcement->title }}</h1>
        <p class="text-sm text-gray-500 mt-2">
            Published {{ \Carbon\Carbon::parse($announcement->published_at)->format('j F Y') }}
        </p>
        <div class="mt-6 prose max-w-none text-gray-800">
            {!! nl2br(e($announcement->body)) !!}
        </div>
    </article>
</div>
@endsection
