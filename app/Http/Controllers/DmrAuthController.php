<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
class DmrAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $returnUrl = $request->query('return', env('DMR_DASHBOARD_URL', 'https://m0kkn.dragon-net.pl:8010') . '/auth.php');
        if (!auth()->check()) {
            $intended = url('/dmr-auth') . '?return=' . urlencode($returnUrl);
            session(['url.intended' => $intended]);
            return redirect()->route('login');
        }
        return $this->issue($request, $returnUrl);
    }

    public function issue(Request $request, $returnUrl = null)
    {
        $user = auth()->user();

        if (!$user->hasDirectPermission('view dmr dashboard') && !$user->roles->flatMap->permissions->contains('name', 'view dmr dashboard')) {
            return response()->view('errors.dmr-access-denied', [], 403);
        }

        // Spatie roles as a simple array of names
        $spatieRoles = $user->roles->pluck('name')->toArray();

        // All DMR-related permissions the user holds
        $dmrPermissions = $user->getAllPermissions()
            ->filter(fn($p) => str_starts_with($p->name, 'dmr'))
            ->pluck('name')
            ->toArray();

        // Member roles from the custom member_roles table (via pivot)
        $memberRoles = $user->memberRoles()
            ->where('is_active', true)
            ->pluck('name')
            ->toArray();

        // Avatar — falls back to Gravatar if no local avatar stored
        $avatar = $user->avatar
            ? asset('storage/' . $user->avatar)
            : 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user->email))) . '?d=mp&s=128';

        $token = Str::random(64);
        Cache::put('dmr_token_' . $token, [
            'user_id'      => $user->id,
            'callsign'     => $user->callsign ?? $user->name,
            'name'         => $user->name,
            'email'        => $user->email,
            'spatie_roles' => $spatieRoles,
            'dmr_perms'    => $dmrPermissions,
            'member_roles' => $memberRoles,
            'avatar'       => $avatar,
            'issued'       => now()->timestamp,
        ], 60);

        if (!$returnUrl) {
            $returnUrl = session('dmr_return', env('DMR_DASHBOARD_URL', 'https://m0kkn.dragon-net.pl:8010') . '/auth.php');
            session()->forget('dmr_return');
        }

        return redirect($returnUrl . '?token=' . $token);
    }

    public function validateToken(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return response()->json([
                'valid'  => false,
                'reason' => 'No token provided',
            ], 401);
        }

        $data = Cache::get('dmr_token_' . $token);

        if (!$data) {
            return response()->json([
                'valid'  => false,
                'reason' => 'Token expired or invalid',
            ], 401);
        }

        Cache::forget('dmr_token_' . $token);

        return response()->json([
            'valid'        => true,
            'user_id'      => $data['user_id'],
            'callsign'     => $data['callsign'],
            'name'         => $data['name'],
            'email'        => $data['email'],
            'spatie_roles' => $data['spatie_roles'],
            'dmr_perms'    => $data['dmr_perms'],
            'member_roles' => $data['member_roles'],
            'avatar'       => $data['avatar'],
        ]);
    }
}