<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Authorise Application — {{ \App\Helpers\RaynetSetting::groupName() }}</title>
    <style>
        :root {
            --navy:#003366; --red:#C8102E; --light:#F2F2F2;
            --border:#D0D0D0; --muted:#6b7f96;
            --green:#1a6b3c; --green-bg:#eef7f2;
        }
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family: Arial,'Helvetica Neue',Helvetica,sans-serif;
            background: var(--light); min-height:100vh;
            display:flex; align-items:center; justify-content:center;
            padding:1.5rem;
        }
        .card {
            background:#fff; border:1px solid var(--border);
            border-radius:10px; width:100%; max-width:460px;
            overflow:hidden;
            box-shadow:0 4px 24px rgba(0,51,102,.13);
        }
        .card-header {
            background:var(--navy); padding:1.5rem 1.5rem 1.2rem;
            display:flex; flex-direction:column; align-items:center; gap:.65rem;
        }
        .header-logo { height:36px; width:auto; }
        .header-title {
            font-size:.8rem; font-weight:bold; color:rgba(255,255,255,.55);
            text-transform:uppercase; letter-spacing:.12em;
        }
        .card-body { padding:1.5rem; }

        /* App info */
        .app-block {
            display:flex; align-items:center; gap:.85rem;
            padding:1rem; background:var(--light);
            border:1px solid var(--border); border-radius:8px; margin-bottom:1.25rem;
        }
        .app-icon {
            width:48px; height:48px; border-radius:8px;
            background:var(--navy); display:flex; align-items:center;
            justify-content:center; font-size:1.4rem; flex-shrink:0;
        }
        .app-name { font-size:1.1rem; font-weight:bold; color:var(--navy); }
        .app-desc { font-size:.82rem; color:var(--muted); margin-top:2px; }
        .app-url  { font-size:.75rem; color:var(--muted); margin-top:2px; font-family:monospace; }

        /* Arrow connector */
        .connector {
            display:flex; align-items:center; justify-content:center;
            gap:.5rem; margin-bottom:1.25rem; font-size:.82rem; color:var(--muted);
        }
        .connector-arrow { font-size:1.2rem; color:var(--navy); }

        /* Scopes */
        .scopes-label {
            font-size:.75rem; font-weight:bold; text-transform:uppercase;
            letter-spacing:.1em; color:var(--muted); margin-bottom:.6rem;
        }
        .scopes-list { display:flex; flex-direction:column; gap:.5rem; margin-bottom:1.5rem; }
        .scope-item {
            display:flex; align-items:flex-start; gap:.75rem;
            padding:.65rem .85rem; border-radius:6px;
            background:var(--green-bg); border:1px solid rgba(26,107,60,.2);
        }
        .scope-tick { color:var(--green); font-size:1rem; flex-shrink:0; margin-top:1px; }
        .scope-name { font-size:.88rem; font-weight:bold; color:var(--navy); }
        .scope-desc { font-size:.78rem; color:var(--muted); margin-top:1px; }

        /* User strip */
        .user-strip {
            display:flex; align-items:center; gap:.65rem;
            padding:.75rem; background:rgba(0,51,102,.04);
            border:1px solid var(--border); border-radius:6px; margin-bottom:1.25rem;
        }
        .user-avatar {
            width:36px; height:36px; border-radius:50%; background:var(--navy);
            display:flex; align-items:center; justify-content:center;
            font-size:13px; font-weight:bold; color:#fff; flex-shrink:0;
            overflow:hidden; border:1.5px solid rgba(0,51,102,.15);
        }
        .user-avatar img { width:100%; height:100%; object-fit:cover; }
        .user-name { font-size:.9rem; font-weight:bold; color:var(--navy); }
        .user-call { font-size:.75rem; color:var(--muted); }
        .user-not-you {
            margin-left:auto; font-size:.75rem; color:var(--red);
            text-decoration:none; font-weight:bold; white-space:nowrap;
        }
        .user-not-you:hover { text-decoration:underline; }

        /* Actions */
        .actions { display:flex; gap:.75rem; }
        .btn-approve {
            flex:1; padding:.75rem; background:var(--navy); border:none;
            color:#fff; font-size:.95rem; font-weight:bold;
            border-radius:6px; cursor:pointer; font-family:inherit;
            transition:background .15s;
        }
        .btn-approve:hover { background:#002244; }
        .btn-deny {
            padding:.75rem 1.2rem; background:#fff;
            border:1px solid var(--border); color:var(--muted);
            font-size:.95rem; font-weight:bold; border-radius:6px;
            cursor:pointer; font-family:inherit; transition:all .15s;
        }
        .btn-deny:hover { border-color:var(--red); color:var(--red); background:#fff8f8; }

        /* Footer */
        .card-footer {
            padding:.85rem 1.5rem; background:var(--light);
            border-top:1px solid var(--border);
            font-size:.75rem; color:var(--muted); text-align:center;
        }
        .card-footer a { color:var(--navy); }

        /* Warning */
        .warning-strip {
            display:flex; align-items:flex-start; gap:.6rem;
            padding:.7rem .85rem; background:#fffbeb;
            border:1px solid #fde68a; border-radius:6px; margin-bottom:1.25rem;
            font-size:.78rem; color:#92400e;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <img src="{{ asset('images/raynet-uk-liverpool-banner.png') }}" alt="{{ \App\Helpers\RaynetSetting::groupName() }}" class="header-logo">
        <div class="header-title">Single Sign-On</div>
    </div>

    <div class="card-body">

        {{-- App requesting access --}}
        <div class="app-block">
            <div class="app-icon">🔌</div>
            <div>
                <div class="app-name">{{ $client->name ?? 'Unknown Application' }}</div>
                @if($client->description ?? null)
                    <div class="app-desc">{{ $client->description }}</div>
                @endif
                @php
                    $displayUri = is_array($client->redirect_uris ?? null) ? ($client->redirect_uris[0] ?? null) : ($client->redirect ?? null);
                @endphp
                @if($displayUri)
                    <div class="app-url">{{ parse_url($displayUri, PHP_URL_HOST) }}</div>
                @endif
            </div>
        </div>

        {{-- Arrow --}}
        <div class="connector">
            <span>This app wants to access your</span>
            <span class="connector-arrow">{{ \App\Helpers\RaynetSetting::groupName() }}</span>
            <span>account</span>
        </div>

        {{-- Scopes requested --}}
        @if(!empty($scopes))
        <div class="scopes-label">Permissions requested</div>
        <div class="scopes-list">
            @php
            $scopeLabels = [
                'openid'   => ['label' => 'OpenID',   'desc' => 'Verify your identity'],
                'profile'  => ['label' => 'Profile',  'desc' => 'Your name, title and avatar'],
                'email'    => ['label' => 'Email',     'desc' => 'Your email address'],
                'callsign' => ['label' => 'Callsign',  'desc' => 'Your callsign, DMR ID and licence class'],
                'role'     => ['label' => 'Role',      'desc' => 'Your RAYNET role and permission level'],
            ];
            @endphp
            @foreach($scopes as $scope)
            @php
                $scopeId   = is_object($scope) ? ($scope->id ?? (string)$scope) : (string)$scope;
                $scopeMeta = $scopeLabels[$scopeId] ?? ['label' => ucfirst($scopeId), 'desc' => 'Access to ' . $scopeId];
            @endphp
            <div class="scope-item">
                <span class="scope-tick">✓</span>
                <div>
                    <div class="scope-name">{{ $scopeMeta['label'] }}</div>
                    <div class="scope-desc">{{ $scopeMeta['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Warning for non-RAYNET domains --}}
        @php
            $redirectHost = parse_url(is_array($client->redirect_uris ?? null) ? $client->redirect_uris[0] ?? '' : ($client->redirect ?? ''), PHP_URL_HOST);
            $trusted = in_array($redirectHost, ['raynet-liverpool.net', 'localhost', '127.0.0.1']);
        @endphp
        @unless($trusted)
        <div class="warning-strip">
            ⚠ You are authorising an external application at
            <strong>&nbsp;{{ $redirectHost }}&nbsp;</strong>.
            Only approve if you recognise this application.
        </div>
        @endunless

        {{-- Logged-in user --}}
        @auth
        <div class="user-strip">
            <div class="user-avatar">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-call">
                    {{ auth()->user()->callsign ?? auth()->user()->email }}
                </div>
            </div>
            <a href="{{ route('logout') }}" class="user-not-you"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Not you?
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
        @endauth

        {{-- Approve / Deny forms (Passport expects these exact POST endpoints) --}}
        <div class="actions">
            <form method="POST" action="/oauth/authorize" style="flex:1;">
                @csrf
                {{-- Passport v13 auth token — required for approval validation --}}
                @isset($authToken)
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                @endisset
                {{-- Forward all original query params --}}
                @foreach($request->all() as $key => $val)
                    @if(!in_array($key, ['_token', 'auth_token']))
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endif
                @endforeach
                <button type="submit" class="btn-approve" style="width:100%;">
                    ✓ Authorise Access
                </button>
            </form>
            <form method="POST" action="/oauth/authorize">
                @csrf
                @method('DELETE')
                @isset($authToken)
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                @endisset
                @foreach($request->all() as $key => $val)
                    @if(!in_array($key, ['_token', 'auth_token']))
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endif
                @endforeach
                <button type="submit" class="btn-deny">Deny</button>
            </form>
        </div>

    </div>

    <div class="card-footer">
        Authorised by <a href="/">{{ \App\Helpers\RaynetSetting::groupName() }}</a> ·
        Your credentials are never shared with this application ·
        <a href="{{ route('privacy') }}">Privacy Notice</a>
    </div>
</div>

</body>
</html>