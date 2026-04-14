<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Client;
use Laravel\Passport\Token;

class OAuthController extends Controller
{
    /**
     * OIDC Discovery endpoint
     * GET /.well-known/openid-configuration
     */
    public function discovery(): \Illuminate\Http\JsonResponse
    {
        $base = rtrim(config('app.url'), '/');

        return response()->json([
            'issuer'                                => $base,
            'authorization_endpoint'                => $base . '/oauth/authorize',
            'token_endpoint'                        => $base . '/oauth/token',
            'userinfo_endpoint'                     => $base . '/oauth/userinfo',
            'jwks_uri'                              => $base . '/.well-known/jwks.json',
            'introspection_endpoint'                => $base . '/oauth/introspect',
            'revocation_endpoint'                   => $base . '/oauth/tokens/revoke',
            'end_session_endpoint'                  => $base . '/oauth/logout',
            'scopes_supported'                      => ['openid', 'profile', 'email', 'callsign', 'role'],
            'response_types_supported'              => ['code'],
            'grant_types_supported'                 => ['authorization_code', 'client_credentials', 'refresh_token'],
            'subject_types_supported'               => ['public'],
            'id_token_signing_alg_values_supported' => ['RS256'],
            'token_endpoint_auth_methods_supported' => ['client_secret_post', 'client_secret_basic'],
            'code_challenge_methods_supported'      => ['S256'],
            'claims_supported'                      => [
                'sub', 'iss', 'name', 'email', 'email_verified',
                'picture', 'callsign', 'role', 'roles', 'dmr_id',
                'licence_class', 'operator_title', 'is_admin', 'is_super_admin',
            ],
        ]);
    }

    /**
     * Userinfo endpoint — returns authenticated user's claims.
     * GET /oauth/userinfo
     * Authorization: Bearer <token>
     */
    public function userinfo(Request $request): \Illuminate\Http\JsonResponse
    {
        /** @var \App\Models\User $user */
        $user   = $request->user('api');
        $token  = $user->token();
        $scopes = $token ? $token->scopes : [];

        $claims = [
            'sub' => (string) $user->id,
            'iss' => rtrim(config('app.url'), '/'),
            'iat' => now()->timestamp,
        ];

        // openid is always included if present
        if (in_array('profile', $scopes) || in_array('openid', $scopes)) {
            $claims['name']           = $user->name;
            $claims['operator_title'] = $user->operator_title;
            $claims['picture']        = $user->avatar
                ? Storage::url($user->avatar)
                : null;
        }

        if (in_array('email', $scopes)) {
            $claims['email']          = $user->email;
            $claims['email_verified'] = ! is_null($user->email_verified_at);
        }

        if (in_array('callsign', $scopes)) {
            $claims['callsign']       = $user->callsign;
            $claims['dmr_id']         = $user->dmr_id;
            $claims['licence_class']  = $user->licence_class;
        }

        if (in_array('role', $scopes)) {
            $claims['role']           = $user->getRoleNames()->first() ?? 'member';
            $claims['roles']          = $user->getRoleNames()->values()->toArray();
            $claims['is_admin']       = (bool) $user->is_admin;
            $claims['is_super_admin'] = (bool) $user->is_super_admin;
            $claims['is_committee']   = $user->isCommittee();
        }

        return response()->json($claims);
    }

    /**
     * Custom authorization/consent screen.
     * Passport handles the actual OAuth logic — this just renders the UI.
     * GET /oauth/authorize  (Passport calls this before showing the form)
     */
    public function showAuthorize(Request $request): \Illuminate\Contracts\View\View
    {
        // Passport's built-in authorization already validated the request
        // by the time it calls this view. We just need the client details.
        $clientId = $request->get('client_id');
        $client   = Client::where('id', $clientId)->first();
        $scopes   = $this->resolveScopeDescriptions($request->get('scope', ''));

        return view('auth.oauth.authorize', compact('client', 'scopes', 'request'));
    }

    /**
     * Token introspection endpoint (RFC 7662).
     * POST /oauth/introspect
     */
    public function introspect(Request $request): \Illuminate\Http\JsonResponse
    {
        $tokenId = $request->input('token');
        if (! $tokenId) {
            return response()->json(['active' => false]);
        }

        // Basic auth check — only registered clients can introspect
        [$clientId, $clientSecret] = $this->extractBasicAuth($request);
        if (! $clientId || ! $this->validateClient($clientId, $clientSecret)) {
            return response()->json(['error' => 'invalid_client'], 401);
        }

        $token = Token::with('user')->find($tokenId);

        if (! $token || $token->revoked || $token->expires_at->isPast()) {
            return response()->json(['active' => false]);
        }

        return response()->json([
            'active'     => true,
            'scope'      => implode(' ', $token->scopes),
            'client_id'  => $token->client_id,
            'username'   => $token->user->callsign ?? $token->user->email,
            'exp'        => $token->expires_at->timestamp,
            'sub'        => (string) $token->user_id,
            'iss'        => rtrim(config('app.url'), '/'),
        ]);
    }

    /**
     * SSO logout — revoke token and redirect.
     * GET /oauth/logout
     */
    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            // Revoke all tokens for this user
            $user->tokens()->update(['revoked' => true]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $redirectUri = $request->get('post_logout_redirect_uri', '/');

        // Only allow redirects to registered client URIs
        $allowed = Client::pluck('redirect')->toArray();
        $safeRedirect = collect($allowed)
            ->first(fn($uri) => str_starts_with($redirectUri, $uri));

        return redirect($safeRedirect ?? '/');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resolveScopeDescriptions(string $scopeString): array
    {
        $descriptions = [
            'openid'   => ['label' => 'OpenID',   'desc' => 'Verify your identity'],
            'profile'  => ['label' => 'Profile',  'desc' => 'Your name, title and avatar'],
            'email'    => ['label' => 'Email',     'desc' => 'Your email address'],
            'callsign' => ['label' => 'Callsign',  'desc' => 'Your amateur radio callsign, DMR ID and licence class'],
            'role'     => ['label' => 'Role',      'desc' => 'Your RAYNET role and permission level'],
        ];

        return collect(explode(' ', trim($scopeString)))
            ->filter()
            ->map(fn($s) => array_merge(['scope' => $s], $descriptions[$s] ?? ['label' => $s, 'desc' => 'Access to ' . $s]))
            ->values()
            ->toArray();
    }

    private function extractBasicAuth(Request $request): array
    {
        $header = $request->header('Authorization', '');
        if (str_starts_with($header, 'Basic ')) {
            $decoded = base64_decode(substr($header, 6));
            return explode(':', $decoded, 2) + [null, null];
        }
        return [$request->input('client_id'), $request->input('client_secret')];
    }

    private function validateClient(?string $clientId, ?string $secret): bool
    {
        if (! $clientId || ! $secret) return false;
        $client = Client::find($clientId);
        return $client && hash_equals($client->secret ?? '', $secret);
    }
}
