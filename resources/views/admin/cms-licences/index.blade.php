@extends('layouts.admin')
@section('title', 'CMS Licence Manager')
@section('content')
<style>
.lm-wrap{max-width:960px;margin:0 auto;padding:0 0 4rem}
.lm-card{background:#fff;border:1px solid #dde2e8;border-top:3px solid #003366;box-shadow:0 1px 3px rgba(0,51,102,.09);margin-bottom:1.5rem}
.lm-card-head{padding:.75rem 1.2rem;background:#f4f5f7;border-bottom:1px solid #dde2e8;display:flex;align-items:center;justify-content:space-between}
.lm-card-head h2{font-size:12px;font-weight:bold;color:#003366;text-transform:uppercase;letter-spacing:.06em;margin:0}
.lm-card-body{padding:1.25rem 1.2rem}
.lm-field{display:flex;flex-direction:column;gap:.35rem;margin-bottom:1rem}
.lm-label{font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:#6b7f96}
.lm-input{background:#fff;border:1px solid #dde2e8;padding:.5rem .8rem;font-family:inherit;font-size:13px;color:#111827;outline:none;width:100%;box-sizing:border-box}
.lm-input:focus{border-color:#003366;box-shadow:0 0 0 3px rgba(0,51,102,.08)}
.lm-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.lm-btn{display:inline-flex;align-items:center;gap:.35rem;padding:.45rem 1.1rem;border:1px solid;font-family:inherit;font-size:12px;font-weight:bold;cursor:pointer;transition:all .12s;text-transform:uppercase;letter-spacing:.05em;text-decoration:none}
.lm-btn-primary{background:#003366;border-color:#003366;color:#fff}
.lm-btn-primary:hover{background:#002244}
.lm-btn-danger{background:transparent;border-color:#C8102E;color:#C8102E;font-size:11px;padding:.3rem .7rem}
.lm-btn-danger:hover{background:#fdf0f2}
.lm-btn-ghost{background:transparent;border-color:#dde2e8;color:#6b7f96;font-size:11px;padding:.3rem .7rem}
.lm-foot{padding:.75rem 1.2rem;border-top:1px solid #dde2e8;background:#f4f5f7;display:flex;justify-content:flex-end}
.lm-notice{display:flex;align-items:flex-start;gap:.55rem;padding:.75rem 1rem;margin-bottom:1rem;font-size:12.5px;font-weight:bold}
.lm-notice--ok{background:#eef7f2;border:1px solid #b8ddc9;border-left:3px solid #1a6b3c;color:#1a6b3c}
.lm-notice--err{background:#fdf0f2;border:1px solid rgba(200,16,46,.25);border-left:3px solid #C8102E;color:#C8102E}
.lm-key{font-family:ui-monospace,monospace;font-size:12px;background:#f4f5f7;padding:.25rem .5rem;border:1px solid #dde2e8;color:#003366;font-weight:bold;letter-spacing:.05em;cursor:pointer;user-select:all}
.lm-key:hover{background:#e8eef5}
.lm-table{width:100%;border-collapse:collapse;font-size:13px}
.lm-table th{font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:.1em;color:#9aa3ae;padding:.5rem .75rem;text-align:left;border-bottom:2px solid #dde2e8;white-space:nowrap}
.lm-table td{padding:.6rem .75rem;border-bottom:1px solid #f3f4f6;vertical-align:top}
.lm-table tr:last-child td{border-bottom:none}
.lm-badge{display:inline-flex;align-items:center;gap:.3rem;font-size:11px;font-weight:bold;padding:.2rem .55rem;border-radius:2px}
.lm-badge-active{background:#eef7f2;color:#1a6b3c;border:1px solid #b8ddc9}
.lm-badge-used{background:#fffbeb;color:#92400e;border:1px solid #fde68a}
.lm-badge-revoked{background:#fdf0f2;color:#C8102E;border:1px solid rgba(200,16,46,.2)}
.lm-empty{text-align:center;padding:2rem;color:#9aa3ae;font-size:13px}
</style>

<div class="lm-wrap">

    @if(session('success'))
    <div class="lm-notice lm-notice--ok">
        <span>✓</span>
        <div>
            {{ session('success') }}<br>
            <span style="font-family:ui-monospace,monospace;font-size:13px;letter-spacing:.05em">
                {{ session('success') }}
            </span>
        </div>
    </div>
    @endif
    @if(session('status'))
    <div class="lm-notice lm-notice--ok">✓ {{ session('status') }}</div>
    @endif

    {{-- Generate new licence --}}
    <form method="POST" action="{{ route('admin.cms-licences.store') }}">
        @csrf
        <div class="lm-card">
            <div class="lm-card-head">
                <h2>🔑 Generate New Licence Key</h2>
            </div>
            <div class="lm-card-body">
                <div class="lm-row">
                    <div class="lm-field">
                        <label class="lm-label">Group Name <span style="font-weight:normal;text-transform:none">(required)</span></label>
                        <input type="text" name="group_name" class="lm-input" placeholder="e.g. Manchester RAYNET" required>
                    </div>
                    <div class="lm-field">
                        <label class="lm-label">Group Number</label>
                        <input type="text" name="group_number" class="lm-input" placeholder="e.g. 6/GM/042">
                    </div>
                </div>
                <div class="lm-row">
                    <div class="lm-field">
                        <label class="lm-label">GC Name</label>
                        <input type="text" name="gc_name" class="lm-input" placeholder="e.g. Jane Smith">
                    </div>
                    <div class="lm-field">
                        <label class="lm-label">GC Email</label>
                        <input type="email" name="gc_email" class="lm-input" placeholder="gc@theirgroup.raynet-uk.net">
                    </div>
                </div>
                <div class="lm-field">
                    <label class="lm-label">Notes (optional)</label>
                    <input type="text" name="notes" class="lm-input" placeholder="e.g. Requested via email 15 Apr 2026">
                </div>
            </div>
            <div class="lm-foot">
                <button type="submit" class="lm-btn lm-btn-primary">✓ Generate Licence Key</button>
            </div>
        </div>
    </form>

    {{-- Licence list --}}
    <div class="lm-card">
        <div class="lm-card-head">
            <h2>📋 Issued Licences</h2>
            <span style="font-size:11px;color:#9aa3ae">{{ $licences->count() }} total · {{ $licences->where('activated_at', null)->where('is_active', true)->count() }} available</span>
        </div>
        <div class="lm-card-body" style="padding:0">
            @if($licences->isEmpty())
            <div class="lm-empty">No licences issued yet. Generate one above.</div>
            @else
            <table class="lm-table">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Group</th>
                        <th>GC</th>
                        <th>Status</th>
                        <th>Activated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($licences as $licence)
                <tr>
                    <td>
                        <span class="lm-key" title="Click to copy" onclick="navigator.clipboard.writeText('{{ $licence->key }}');this.textContent='Copied!';setTimeout(()=>this.textContent='{{ $licence->key }}',1500)">
                            {{ $licence->key }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:bold;color:#111827">{{ $licence->group_name }}</div>
                        @if($licence->group_number)
                        <div style="font-size:11px;color:#9aa3ae">{{ $licence->group_number }}</div>
                        @endif
                        @if($licence->notes)
                        <div style="font-size:11px;color:#9aa3ae;font-style:italic">{{ $licence->notes }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:12px">{{ $licence->gc_name }}</div>
                        <div style="font-size:11px;color:#9aa3ae">{{ $licence->gc_email }}</div>
                    </td>
                    <td>
                        @if(!$licence->is_active)
                            <span class="lm-badge lm-badge-revoked">✗ Revoked</span>
                        @elseif($licence->isUsed())
                            <span class="lm-badge lm-badge-used">✓ Used</span>
                            @if($licence->activated_site_url)
                            <div style="font-size:11px;color:#9aa3ae;margin-top:.2rem">{{ $licence->activated_site_url }}</div>
                            @endif
                        @else
                            <span class="lm-badge lm-badge-active">● Available</span>
                        @endif
                    </td>
                    <td style="font-size:11px;color:#9aa3ae">
                        @if($licence->activated_at)
                            {{ $licence->activated_at->format('d M Y') }}<br>
                            {{ $licence->activated_by_ip }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.35rem;flex-wrap:wrap">
                            @if($licence->is_active && !$licence->isUsed())
                            <form method="POST" action="{{ route('admin.cms-licences.revoke', $licence) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="lm-btn lm-btn-danger"
                                    onclick="return confirm('Revoke this licence?')">Revoke</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.cms-licences.destroy', $licence) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="lm-btn lm-btn-ghost"
                                    onclick="return confirm('Delete this licence record?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- API info --}}
    <div class="lm-card">
        <div class="lm-card-head"><h2>🔌 API Endpoint</h2></div>
        <div class="lm-card-body">
            <p style="font-size:13px;color:#374151;margin-bottom:.75rem">
                The installer validates keys against this endpoint:
            </p>
            <code style="display:block;background:#1e1e2e;color:#cdd6f4;padding:.75rem 1rem;font-size:12px;font-family:ui-monospace,monospace">
                POST {{ config('app.url') }}/api/cms/validate-licence<br>
                Body: { "key": "RAYNET-XXXXX-XXXXXXXXXXXXXXXX", "site_url": "https://theirsite.net" }
            </code>
            <p style="font-size:11px;color:#9aa3ae;margin-top:.5rem">
                Returns group details on success and pre-fills the installer form. Key is burned on first use.
            </p>
        </div>
    </div>

</div>
@endsection
