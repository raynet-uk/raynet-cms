<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DmrNetworkController extends Controller
{
    protected function checkAccess(): bool
    {
        $user = auth()->user();
        return $user->hasDirectPermission('view dmr dashboard')
            || $user->roles->flatMap->permissions->contains('name', 'view dmr dashboard')
            || $user->hasDirectPermission('view dmr masters')
            || $user->roles->flatMap->permissions->contains('name', 'view dmr masters');
    }

    protected function hasDashboardAccess(): bool
    {
        $user = auth()->user();
        return $user->hasDirectPermission('view dmr dashboard')
            || $user->roles->flatMap->permissions->contains('name', 'view dmr dashboard');
    }

    protected function hasMastersAccess(): bool
    {
        $user = auth()->user();
        return $user->hasDirectPermission('view dmr masters')
            || $user->roles->flatMap->permissions->contains('name', 'view dmr masters');
    }

    public function index()
    {
        if (!$this->checkAccess()) {
            return response()->view('errors.dmr-access-denied', [], 403);
        }

        return view('members.dmr.index', [
            'hasDashboard' => $this->hasDashboardAccess(),
            'hasMasters'   => $this->hasMastersAccess(),
            'dashboardUrl' => env('DMR_DASHBOARD_URL', 'https://m0kkn.dragon-net.pl:8010'),
            'mastersUrl'   => route('dmr.api.masters'),
            'lastHeardUrl' => route('dmr.api.lastheard'),
        ]);
    }

    // ── Server-side proxy — portal fetches from HTTP DMR server, serves over HTTPS ──

    public function masters()
    {
        if (!$this->checkAccess()) abort(403);

        try {
            $data = Http::timeout(5)
                ->get(env('DMR_DASHBOARD_URL') . '/api/masters.php')
                ->json();
            return response()->json($data ?? []);
        } catch (\Throwable $e) {
            return response()->json([]);
        }
    }

    public function apiLastheard()
    {
        if (!$this->hasDashboardAccess()) abort(403);

        try {
            $data = Http::timeout(5)
                ->get(env('DMR_DASHBOARD_URL') . '/api/lastheard.php')
                ->json();
            return response()->json($data ?? []);
        } catch (\Throwable $e) {
            return response()->json([]);
        }
    }

    public function lastheard()
    {
        if (!$this->hasDashboardAccess()) abort(403);
        return response()->json([]);
    }

    public function peers()
    {
        if (!$this->hasDashboardAccess()) abort(403);
        return response()->json([]);
    }
}