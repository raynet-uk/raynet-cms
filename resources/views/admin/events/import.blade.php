@extends('layouts.app')

@section('title', 'Import Events from CSV')

@section('content')

    <section style="margin-bottom: 1.4rem;">
        <h1 style="margin:0 0 0.4rem; font-size:1.25rem; color:#e5e7eb;">
            Import events from CSV
        </h1>
        <p style="margin:0; font-size:0.9rem; color:#9ca3af; max-width:40rem;">
            Use this tool to bulk-load or restore events. The safest workflow is:
            <strong>export</strong> → edit in Excel/Numbers → <strong>re-import</strong>.
        </p>
    </section>

    {{-- Status message from previous import --}}
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

    {{-- Validation errors --}}
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
            <strong style="display:block; margin-bottom:0.3rem;">There was a problem with the import:</strong>
            <ul style="margin:0; padding-left:1.2rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <article style="
        border-radius:1rem;
        border:1px solid rgba(148,163,184,0.5);
        background:rgba(15,23,42,0.96);
        padding:1rem 1.2rem 1rem;
        font-size:0.9rem;
        color:#e5e7eb;
        max-width:40rem;
    ">
        <h2 style="margin:0 0 0.6rem; font-size:1rem; color:#e5e7eb;">
            Upload CSV file
        </h2>

        <p style="margin:0 0 0.8rem; font-size:0.8rem; color:#9ca3af;">
            The CSV should have the same columns as the <strong>Export events (CSV)</strong> output:
            <code>title, slug, starts_at, ends_at, location, type_name, description, is_sample</code>.
        </p>

        <form method="POST"
              action="{{ route('admin.events.import.process') }}"
              enctype="multipart/form-data"
              style="margin-top:0.4rem;">
            @csrf

            <div style="margin-bottom:0.8rem;">
                <label for="events_file" style="display:block; font-size:0.82rem; margin-bottom:0.2rem;">
                    Events CSV file
                </label>
                <input
                    id="events_file"
                    name="events_file"
                    type="file"
                    accept=".csv,text/csv"
                    required
                    style="
                        display:block;
                        width:100%;
                        padding:0.35rem 0.4rem;
                        border-radius:0.6rem;
                        border:1px solid rgba(148,163,184,0.7);
                        background:#020617;
                        color:#e5e7eb;
                        font-size:0.85rem;
                    "
                >
                <p style="margin:0.25rem 0 0; font-size:0.75rem; color:#6b7280;">
                    Max size 5&nbsp;MB. For safety, always keep a copy of any export you import from.
                </p>
            </div>

            <div style="margin-bottom:0.9rem;">
                <label style="display:flex; align-items:center; gap:0.4rem; font-size:0.8rem;">
                    <input
                        type="checkbox"
                        name="update_existing"
                        value="1"
                        style="width:14px; height:14px;"
                        {{ old('update_existing') ? 'checked' : '' }}
                    >
                    <span>
                        Allow updating existing events with the same <code>slug</code>.
                        <span style="color:#f97373;">(Leave unticked to skip existing records.)</span>
                    </span>
                </label>
            </div>

            <div style="display:flex; gap:0.6rem; align-items:center;">
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

                <a href="{{ route('admin.events') }}"
                   style="font-size:0.8rem; color:#93c5fd; text-decoration:none;">
                    Back to events list →
                </a>
            </div>
        </form>
    </article>
@endsection