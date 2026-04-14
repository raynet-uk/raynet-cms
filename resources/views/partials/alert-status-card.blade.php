@php
    /** @var \App\Models\AlertStatus|null $alertStatus */
    $alertStatus = $alertStatus ?? null;
    $meta        = $alertStatus?->meta();
    $level       = $alertStatus->level ?? 5;
    $colour      = $meta['colour'] ?? '#22c55e';

    // For Level 3 (yellow) use dark text, else white text
    $textColour  = ($level == 3) ? '#111827' : '#f9fafb';
    $subtleText  = ($level == 3) ? '#1f2937' : 'rgba(248,250,252,0.85)';
@endphp

<div style="
    max-width: 360px;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(15,23,42,0.75);
    border: 1px solid rgba(15,23,42,0.95);
    background: #020617;
    font-size: 0.88rem;
">
    {{-- Header strip --}}
    <div style="
        display:flex;
        background:#020617;
        border-bottom:1px solid rgba(15,23,42,0.95);
    ">
        {{-- Left label --}}
        <div style="
            flex:1;
            padding:0.4rem 0.9rem;
            background:#0b1b3b;
            color:#e5e7eb;
            font-size:0.75rem;
            text-transform:uppercase;
            letter-spacing:0.12em;
            font-weight:600;
            border-right:1px solid rgba(15,23,42,0.95);
            border-top-left-radius:1rem;
        ">
            Liverpool RAYNET
        </div>

        {{-- Centre level box --}}
       <div style="
            flex:0 0 80px;
            display:flex;
            justify-content:center;      /* horizontal centring */
            align-items:center;          /* vertical centring */
            background:#000000;
            color:#fff;
            font-size:2.6rem;            /* adjust size here */
            font-weight:800;
            padding-top:2px;             /* NU D G E  UP/DOWN */
            letter-spacing:-1px;
        ">
            {{ $level }}
        </div>

        {{-- Right label --}}
        <div style="
            flex:1;
            padding:0.4rem 0.9rem;
            background:#020617;
            color:#cbd5f5;
            font-size:0.75rem;
            text-transform:uppercase;
            letter-spacing:0.12em;
            font-weight:600;
            text-align:right;
            border-top-right-radius:1rem;
        ">
            RAYNET Status
        </div>
    </div>

    {{-- Body --}}
    <div style="
        padding:0.95rem 1.05rem 1.05rem;
        background: {{ $colour }};
        color: {{ $textColour }};
    ">
        {{-- Level line --}}
        <div style="font-size:1.05rem; font-weight:700; margin-bottom:0.2rem;">
            Level {{ $level }}
        </div>

        {{-- Title --}}
        <div style="font-weight:600; margin-bottom:0.3rem;">
            {{ $meta['title'] ?? 'Alert Level '.$level }}
        </div>

        {{-- Default description --}}
        <div style="font-size:0.86rem; margin-bottom:0.45rem; color: {{ $subtleText }};">
            {{ $meta['description'] ?? '' }}
        </div>

        {{-- Custom message --}}
        @if(!empty($alertStatus?->message))
            <div style="
                margin-top:0.45rem;
                padding-top:0.45rem;
                border-top:1px dashed rgba(15,23,42,0.3);
                font-size:0.84rem;
                font-weight:500;
                color: {{ $textColour }};
                background:rgba(255,255,255,0.22);
                border-radius:0.4rem;
                padding:0.45rem 0.6rem;
            ">
                {{ $alertStatus->message }}
            </div>
        @endif

        {{-- Status line --}}
       
    </div>
</div>