@extends('layouts.admin')

@section('title', 'Import Events')

@section('content')

    <section style="margin-bottom:1.2rem;">
        <h1 style="margin:0 0 0.4rem; font-size:1.25rem; color:#e5e7eb;">
            Import events from CSV
        </h1>
        <p style="margin:0; font-size:0.9rem; color:#9ca3af;">
            Upload a CSV file in the same format as the event export. Existing IDs will be updated,
            new rows will be created as fresh events.
        </p>
    </section>

    @if (session('status'))
        <div style="
            margin-bottom:0.9rem;
            padding:0.6rem 0.8rem;
            border-radius:0.75rem;
            border:1px solid rgba(34,197,94,0.7);
            background:rgba(22,163,74,0.18);
            color:#bbf7d0;
            font-size:0.85rem;
        ">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="
            margin-bottom:0.9rem;
            padding:0.6rem 0.8rem;
            border-radius:0.75rem;
            border:1px solid rgba(248,113,113,0.7);
            background:rgba(220,38,38,0.18);
            color:#fecaca;
            font-size:0.85rem;
        ">
            <strong style="display:block; margin-bottom:0.3rem;">Please fix the following:</strong>
            <ul style="margin:0; padding-left:1.2rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.events.import') }}" enctype="multipart/form-data">
        @csrf

        <div style="margin-bottom:0.8rem;">
            <label for="file" style="display:block; font-size:0.85rem; margin-bottom:0.25rem;">
                CSV file
            </label>
            <input
                id="file"
                name="file"
                type="file"
                accept=".csv,text/csv"
                required
                style="font-size:0.85rem; color:#e5e7eb;"
            >
            <p style="margin:0.25rem 0 0; font-size:0.78rem; color:#6b7280;">
                Export first to see the expected column layout: id, title, slug, starts_at, ends_at,
                location, description, event_type_slug, is_public.
            </p>
        </div>

        <button type="submit" style="
            padding:0.45rem 0.95rem;
            border-radius:999px;
            border:none;
            background:linear-gradient(to right,#38bdf8,#0ea5e9);
            color:#020617;
            font-size:0.9rem;
            font-weight:600;
            cursor:pointer;
        ">
            Import events
        </button>
    </form>

@endsection