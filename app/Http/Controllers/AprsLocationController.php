<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AprsService;
use Illuminate\Http\Request;

class AprsLocationController extends Controller
{
    public function __construct(private AprsService $aprs) {}

    public function index(Request $request)
    {
        // Fetch all users with a callsign, excluding test accounts
        $users = User::whereNotNull('callsign')
            ->where('callsign', '!=', '')
            ->where('callsign', '!=', 'TEST')
            ->whereNull('suspended_at')
            ->orderBy('name')
            ->get(['id', 'name', 'callsign', 'aprs_ssid', 'operator_title', 'avatar']);

        // Build the full list of callsigns to query (base + SSID variants)
        $callsigns = $this->aprs->buildCallsignList($users);

        // Hit the APRS.fi API
        $locations = $this->aprs->getLocations($callsigns);

        // Attach location data to each user (may have base + SSID variants)
        $userRows = $users->map(function ($user) use ($locations) {
            $base  = strtoupper($user->callsign ?? '');
            $ssid  = trim($user->aprs_ssid ?? '');
            $with  = ($ssid !== '' && $ssid !== '0') ? $base . '-' . ltrim($ssid, '-') : null;

            // Prefer SSID variant if available, otherwise base callsign
            $entry = null;
            if ($with && isset($locations[$with])) {
                $entry = $locations[$with];
            } elseif (isset($locations[$base])) {
                $entry = $locations[$base];
            }

            // Also collect any other SSIDs found (e.g. -1 through -15)
            $extras = [];
            foreach ($locations as $key => $loc) {
                if (str_starts_with($key, $base . '-') && $key !== ($with ?? '')) {
                    $extras[$key] = $loc;
                }
            }

            return [
                'user'      => $user,
                'entry'     => $entry,
                'extras'    => $extras,
                'located'   => $entry !== null,
            ];
        });

        $located = $userRows->where('located', true)->count();

        return view('admin.aprs-locations', compact('userRows', 'located', 'locations'));
    }

    /**
     * JSON endpoint for live refresh via JS.
     */
    public function refresh(Request $request)
    {
        $users     = User::whereNotNull('callsign')
            ->where('callsign', '!=', '')
            ->where('callsign', '!=', 'TEST')
            ->whereNull('suspended_at')
            ->get(['id', 'callsign', 'aprs_ssid']);

        $callsigns = $this->aprs->buildCallsignList($users);
        $locations = $this->aprs->getLocations($callsigns);

        return response()->json([
            'locations'  => $locations,
            'count'      => count($locations),
            'refreshed'  => now()->format('H:i:s'),
        ]);
    }
}
