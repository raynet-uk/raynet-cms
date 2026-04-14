@extends('layouts.app')
@section('title', 'SSO — OAuth Clients')

@section('content')
<style>
:root{--navy:#003366;--red:#C8102E;--light:#F2F2F2;--border:#D0D0D0;--muted:#6b7f96;--shadow-sm:0 2px 8px rgba(0,51,102,.06);}
.oa-wrap{max-width:1200px;margin:0 auto;padding:0 1rem 3rem;}
.oa-header{display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;padding:1rem 0;border-bottom:2px solid var(--navy);margin-bottom:1.5rem;}
.oa-title{font-size:1.5rem;font-weight:bold;color:var(--navy);}
.oa-sub{font-size:.85rem;color:var(--muted);margin-top:2px;}
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.1rem;border-radius:6px;font-size:.85rem;font-weight:bold;font-family:inherit;cursor:pointer;border:none;transition:background .15s;}
.btn-navy{background:var(--navy);color:#fff;}.btn-navy:hover{background:#002244;}
.btn-outline{background:#fff;border:1px solid var(--border);color:var(--navy);}.btn-outline:hover{border-color:var(--navy);background:#f0f5ff;}
.btn-red{background:var(--red);color:#fff;}.btn-red:hover{background:#9e0b23;}
.btn-sm{padding:.3rem .75rem;font-size:.78rem;}

/* secret modal */
.secret-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:2000;align-items:center;justify-content:center;padding:1rem;}
.secret-modal.open{display:flex;}
.secret-box{background:#fff;border-radius:10px;padding:1.75rem;max-width:500px;width:100%;box-shadow:0 8px 32px rgba(0,0,0,.25);}
.secret-title{font-size:1.15rem;font-weight:bold;color:var(--navy);margin-bottom:.5rem;}
.secret-warn{font-size:.85rem;color:#92400e;background:#fffbeb;border:1px solid #fde68a;border-radius:6px;padding:.65rem .85rem;margin-bottom:1rem;}
.secret-field{display:flex;gap:.5rem;margin-bottom:1.2rem;}
.secret-field input{flex:1;padding:.55rem .8rem;border:1px solid var(--border);border-radius:6px;font-family:monospace;font-size:.85rem;color:var(--navy);background:#f8f8f8;}
.secret-close{display:block;width:100%;padding:.65rem;background:var(--navy);color:#fff;border:none;border-radius:6px;font-size:.9rem;font-weight:bold;cursor:pointer;font-family:inherit;}

/* client cards */
.clients-grid{display:flex;flex-direction:column;gap:1rem;margin-bottom:2rem;}
.client-card{background:#fff;border:1px solid var(--border);border-radius:8px;overflow:hidden;box-shadow:var(--shadow-sm);}
.client-card-head{display:flex;align-items:center;justify-content:space-between;padding:.85rem 1.2rem;background:var(--light);border-bottom:1px solid var(--border);gap:1rem;flex-wrap:wrap;}
.client-name{font-size:1rem;font-weight:bold;color:var(--navy);}
.client-meta{font-size:.78rem;color:var(--muted);}
.client-actions{display:flex;gap:.5rem;flex-wrap:wrap;}
.client-body{padding:1.1rem 1.2rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
@media(max-width:600px){.client-body{grid-template-columns:1fr;}}
.client-field-label{font-size:.72rem;font-weight:bold;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:3px;}
.client-field-value{font-size:.85rem;color:var(--navy);font-family:monospace;word-break:break-all;}
.client-field-value.normal{font-family:Arial,sans-serif;}
.scope-chip{display:inline-block;font-size:.7rem;font-weight:bold;padding:2px 7px;border-radius:999px;background:#e8eef5;border:1px solid var(--navy);color:var(--navy);margin:2px 2px 0 0;}
.token-count{display:inline-flex;align-items:center;gap:.35rem;padding:2px 8px;border-radius:999px;background:rgba(0,51,102,.08);font-size:.75rem;font-weight:bold;color:var(--navy);}

/* New client form */
.form-card{background:#fff;border:1px solid var(--border);border-radius:8px;overflow:hidden;box-shadow:var(--shadow-sm);margin-bottom:2rem;display:none;}
.form-card.open{display:block;}
.form-head{padding:.85rem 1.2rem;background:var(--light);border-bottom:1px solid var(--border);font-size:1rem;font-weight:bold;color:var(--navy);}
.form-body{padding:1.2rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
@media(max-width:600px){.form-body{grid-template-columns:1fr;}}
.form-full{grid-column:1/-1;}
.form-group{display:flex;flex-direction:column;gap:.35rem;}
label{font-size:.8rem;font-weight:bold;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;}
input[type=text],input[type=url],textarea,select{padding:.55rem .8rem;border:1px solid var(--border);border-radius:6px;font-size:.88rem;font-family:inherit;color:var(--navy);outline:none;transition:border-color .15s;}
input[type=text]:focus,input[type=url]:focus,textarea:focus{border-color:var(--navy);}
.scope-checks{display:flex;flex-wrap:wrap;gap:.75rem;margin-top:.25rem;}
.scope-check{display:flex;align-items:center;gap:.4rem;font-size:.85rem;font-weight:bold;color:var(--navy);cursor:pointer;}
.scope-check input{cursor:pointer;}
.form-actions{grid-column:1/-1;display:flex;gap:.75rem;justify-content:flex-end;padding-top:.5rem;border-top:1px solid var(--border);margin-top:.5rem;}

/* Docs section */
.docs-card{background:#fff;border:1px solid var(--border);border-radius:8px;overflow:hidden;box-shadow:var(--shadow-sm);}
.docs-head{padding:.85rem 1.2rem;background:var(--light);border-bottom:1px solid var(--border);font-size:1rem;font-weight:bold;color:var(--navy);}
.docs-body{padding:1.2rem;}
.endpoint-row{display:flex;align-items:center;gap:.75rem;padding:.65rem 0;border-bottom:1px solid var(--border);}
.endpoint-row:last-child{border-bottom:none;}
.endpoint-method{font-size:.72rem;font-weight:bold;padding:2px 7px;border-radius:3px;background:var(--navy);color:#fff;white-space:nowrap;font-family:monospace;}
.endpoint-method.get{background:#1a6b3c;}
.endpoint-method.post{background:#003366;}
.endpoint-url{font-size:.83rem;font-family:monospace;color:var(--navy);}
.endpoint-desc{font-size:.78rem;color:var(--muted);margin-left:auto;white-space:nowrap;}
</style>

<div class="oa-wrap">

    <div class="oa-header">
        <div>
            <div class="oa-title">🔐 SSO — OAuth 2.0 Clients</div>
            <div class="oa-sub">Manage applications that can authenticate against the Liverpool RAYNET portal</div>
        </div>
        <button class="btn btn-navy" onclick="document.getElementById('newClientForm').classList.toggle('open')">
            + Register new client
        </button>
    </div>

    {{-- Secret reveal modal --}}
    @if(session('sso_new_client'))
    @php $nc = session('sso_new_client'); @endphp
    <div class="secret-modal open" id="secretModal">
        <div class="secret-box">
            <div class="secret-title">🔑 Client credentials — save these now</div>
            <div class="secret-warn">
                ⚠ The client secret is shown <strong>once only</strong> and cannot be retrieved again.
                Copy it now and store it securely.
            </div>
            <div class="form-group" style="margin-bottom:.75rem;">
                <label>Client name</label>
                <input type="text" value="{{ $nc['name'] }}" readonly>
            </div>
            <div class="form-group" style="margin-bottom:.75rem;">
                <label>Client ID</label>
                <div class="secret-field">
                    <input type="text" id="clientIdField" value="{{ $nc['id'] }}" readonly>
                    <button class="btn btn-outline btn-sm" onclick="copy('clientIdField')">Copy</button>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:1.2rem;">
                <label>Client secret</label>
                <div class="secret-field">
                    <input type="text" id="clientSecretField" value="{{ $nc['secret'] }}" readonly>
                    <button class="btn btn-outline btn-sm" onclick="copy('clientSecretField')">Copy</button>
                </div>
            </div>
            <button class="secret-close" onclick="document.getElementById('secretModal').classList.remove('open')">
                I have saved these credentials — close
            </button>
        </div>
    </div>
    @endif

    {{-- Flash --}}
    @if(session('success'))
    <div style="padding:.75rem 1rem;background:#eef7f2;border:1px solid rgba(26,107,60,.25);border-radius:6px;color:#1a6b3c;font-weight:bold;font-size:.88rem;margin-bottom:1rem;">
        ✓ {{ session('success') }}
    </div>
    @endif

    {{-- New client form --}}
    <div class="form-card" id="newClientForm">
        <div class="form-head">Register new OAuth client</div>
        <form method="POST" action="{{ route('admin.oauth.clients.store') }}">
            @csrf
            <div class="form-body">
                <div class="form-group">
                    <label for="name">Application name *</label>
                    <input type="text" name="name" id="name" placeholder="HBMon Dashboard" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label for="redirect">Redirect URI *</label>
                    <input type="url" name="redirect" id="redirect" placeholder="https://app.example.com/auth/callback" required value="{{ old('redirect') }}">
                </div>
                <div class="form-group form-full">
                    <label for="description">Description (optional)</label>
                    <input type="text" name="description" id="description" placeholder="Brief description of this application" value="{{ old('description') }}">
                </div>
                <div class="form-group form-full">
                    <label>Scopes *</label>
                    <div class="scope-checks">
                        @foreach(['openid' => 'OpenID (required)', 'profile' => 'Profile', 'email' => 'Email', 'callsign' => 'Callsign', 'role' => 'Role'] as $scope => $label)
                        <label class="scope-check">
                            <input type="checkbox" name="scopes[]" value="{{ $scope }}"
                                {{ in_array($scope, old('scopes', ['openid','profile'])) ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('newClientForm').classList.remove('open')">Cancel</button>
                    <button type="submit" class="btn btn-navy">Register client</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Client list --}}
    <div class="clients-grid">
        @forelse($clients as $client)
        @php
            $activeTokens = $client->tokens->where('revoked', false)->where('expires_at', '>', now())->count();
        @endphp
        <div class="client-card">
            <div class="client-card-head">
                <div>
                    <div class="client-name">{{ $client->name }}</div>
                    <div class="client-meta">
                        ID: <code>{{ $client->id }}</code>
                        · Registered {{ $client->created_at->diffForHumans() }}
                        @if($client->description) · {{ $client->description }} @endif
                    </div>
                </div>
                <div class="client-actions">
                    @if($activeTokens > 0)
                    <a href="{{ route('admin.oauth.tokens', $client->id) }}" class="btn btn-outline btn-sm">
                        <span class="token-count">{{ $activeTokens }} tokens</span>
                    </a>
                    @endif
                    <button class="btn btn-outline btn-sm"
                            onclick="toggleEdit('edit-{{ $client->id }}')">Edit</button>
                    <form method="POST" action="{{ route('admin.oauth.clients.rotate', $client->id) }}"
                          onsubmit="return confirm('Rotate secret? All existing integrations must be updated.')">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-outline btn-sm">Rotate secret</button>
                    </form>
                    <form method="POST" action="{{ route('admin.oauth.clients.revoke', $client->id) }}"
                          onsubmit="return confirm('Revoke this client? All active tokens will be invalidated immediately.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-red btn-sm">Revoke</button>
                    </form>
                </div>
            </div>

            <div class="client-body">
                <div>
                    <div class="client-field-label">Redirect URI</div>
                    <div class="client-field-value">{{ $client->redirect }}</div>
                </div>
                <div>
                    <div class="client-field-label">Client secret</div>
                    <div class="client-field-value">••••••••••••••••••••</div>
                </div>
            </div>

            {{-- Inline edit form --}}
            <div id="edit-{{ $client->id }}" style="display:none;border-top:1px solid var(--border);">
                <form method="POST" action="{{ route('admin.oauth.clients.update', $client->id) }}">
                    @csrf @method('PUT')
                    <div class="form-body">
                        <div class="form-group">
                            <label>Application name</label>
                            <input type="text" name="name" value="{{ $client->name }}" required>
                        </div>
                        <div class="form-group">
                            <label>Redirect URI</label>
                            <input type="url" name="redirect" value="{{ $client->redirect }}" required>
                        </div>
                        <div class="form-group form-full">
                            <label>Description</label>
                            <input type="text" name="description" value="{{ $client->description }}">
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-outline" onclick="toggleEdit('edit-{{ $client->id }}')">Cancel</button>
                            <button type="submit" class="btn btn-navy">Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div style="padding:2rem;text-align:center;color:var(--muted);background:#fff;border:1px solid var(--border);border-radius:8px;">
            No clients registered yet. Click "Register new client" to get started.
        </div>
        @endforelse
    </div>

    {{-- Endpoint reference --}}
    <div class="docs-card">
        <div class="docs-head">🔗 Endpoint reference</div>
        <div class="docs-body">
            @php $base = config('app.url'); @endphp
            @foreach([
                ['GET',  '/.well-known/openid-configuration', 'OIDC discovery document'],
                ['GET',  '/oauth/authorize',                  'Authorization endpoint'],
                ['POST', '/oauth/token',                      'Token endpoint'],
                ['GET',  '/oauth/userinfo',                   'Userinfo endpoint (Bearer token required)'],
                ['POST', '/oauth/introspect',                 'Token introspection (RFC 7662)'],
                ['POST', '/oauth/tokens/revoke',              'Token revocation'],
                ['GET',  '/oauth/logout',                     'SSO logout / end session'],
            ] as [$method, $path, $desc])
            <div class="endpoint-row">
                <span class="endpoint-method {{ strtolower($method) }}">{{ $method }}</span>
                <span class="endpoint-url">{{ $base }}{{ $path }}</span>
                <span class="endpoint-desc">{{ $desc }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleEdit(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
function copy(fieldId) {
    const el = document.getElementById(fieldId);
    el.select();
    document.execCommand('copy');
    alert('Copied!');
}
</script>
@endpush
