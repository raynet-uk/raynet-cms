<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

class OAuthClientController extends Controller
{
    public function __construct(private ClientRepository $clients) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        $clients = Client::with('tokens')
            ->where('revoked', false)
            ->orderBy('name')
            ->get();

        return view('admin.oauth.clients', compact('clients'));
    }

    /**
     * Passport v13 oauth_clients schema:
     * id, owner_type, owner_id, name, secret, provider,
     * redirect_uris (JSON), grant_types (JSON), revoked, timestamps
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'redirect'    => ['required', 'url', 'max:500'],
            'scopes'      => ['required', 'array'],
            'scopes.*'    => ['in:openid,profile,email,callsign,role'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $plainSecret = Str::random(40);

        $client = Client::create([
            'name'          => $validated['name'],
            'secret'        => $plainSecret,
            'redirect_uris' => [$validated['redirect']],
            'grant_types'   => ['authorization_code', 'refresh_token'],
            'revoked'       => false,
        ]);

        \App\Helpers\AuditLogger::log('oauth_client_created', null, 'OAuth client created', [
            'client_id'   => $client->id,
            'client_name' => $client->name,
            'redirect'    => $validated['redirect'],
            'scopes'      => $validated['scopes'],
        ]);

        return redirect()->route('admin.oauth.clients')
            ->with('sso_new_client', [
                'name'   => $client->name,
                'id'     => $client->id,
                'secret' => $plainSecret,
            ]);
    }

    public function update(Request $request, string $id): \Illuminate\Http\RedirectResponse
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'redirect' => ['required', 'url', 'max:500'],
        ]);

        $client->update([
            'name'          => $validated['name'],
            'redirect_uris' => [$validated['redirect']],
        ]);

        \App\Helpers\AuditLogger::log('oauth_client_updated', null, 'OAuth client updated', [
            'client_id'   => $client->id,
            'client_name' => $client->name,
        ]);

        return redirect()->route('admin.oauth.clients')->with('success', 'Client updated.');
    }

    public function rotateSecret(string $id): \Illuminate\Http\RedirectResponse
    {
        $client      = Client::findOrFail($id);
        $plainSecret = Str::random(40);

        $client->update(['secret' => $plainSecret]);

        \App\Helpers\AuditLogger::log('oauth_client_secret_rotated', null, 'OAuth client secret rotated', [
            'client_id'   => $client->id,
            'client_name' => $client->name,
        ]);

        return redirect()->route('admin.oauth.clients')
            ->with('sso_new_client', [
                'name'   => $client->name,
                'id'     => $client->id,
                'secret' => $plainSecret,
            ]);
    }

    public function revoke(string $id): \Illuminate\Http\RedirectResponse
    {
        $client = Client::findOrFail($id);
        $client->update(['revoked' => true]);
        $client->tokens()->update(['revoked' => true]);

        \App\Helpers\AuditLogger::log('oauth_client_revoked', null, 'OAuth client revoked', [
            'client_id'   => $client->id,
            'client_name' => $client->name,
        ]);

        return redirect()->route('admin.oauth.clients')
            ->with('success', 'Client revoked and all tokens invalidated.');
    }

    public function tokens(string $id): \Illuminate\Contracts\View\View
    {
        $client = Client::findOrFail($id);
        $tokens = $client->tokens()
            ->with('user')
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.oauth.tokens', compact('client', 'tokens'));
    }

    public function revokeToken(string $clientId, string $tokenId): \Illuminate\Http\RedirectResponse
    {
        $token = \Laravel\Passport\Token::where('id', $tokenId)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $token->update(['revoked' => true]);

        return redirect()->back()->with('success', 'Token revoked.');
    }
}