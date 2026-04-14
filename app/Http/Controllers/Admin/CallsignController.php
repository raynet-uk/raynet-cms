<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\AuditLogger;

class CallsignController extends Controller
{
    public function approve($id)
    {
        $user = User::findOrFail($id);

        if ($user->pending_callsign) {
            $oldCallsign = $user->callsign;
            $newCallsign = $user->pending_callsign;

            $user->callsign         = $newCallsign;
            $user->pending_callsign = null;
            $user->save();

            AuditLogger::log(
                'user.callsign_approved',
                $user,
                "Callsign approved for {$user->name}: " . ($oldCallsign ?? 'none') . " → {$newCallsign}",
                ['callsign' => $oldCallsign, 'pending_callsign' => $newCallsign],
                ['callsign' => $newCallsign, 'pending_callsign' => null]
            );
        }

        return redirect()->back()->with('status', "Callsign {$user->callsign} approved for {$user->name}.");
    }

    public function reject($id)
    {
        $user = User::findOrFail($id);

        $rejectedCallsign = $user->pending_callsign;

        $user->pending_callsign = null;
        $user->save();

        AuditLogger::log(
            'user.callsign_rejected',
            $user,
            "Callsign request rejected for {$user->name}: {$rejectedCallsign} was denied",
            ['pending_callsign' => $rejectedCallsign],
            ['pending_callsign' => null]
        );

        return redirect()->back()->with('status', "Callsign request rejected for {$user->name}.");
    }
}