<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Helpers\AuditLogger;

class AdminMessageController extends Controller
{
    // ── SEND PERSONAL MESSAGE TO ONE USER ────────────────────────────────────
    public function send(User $user, Request $request)
    {
        $request->validate([
            'admin_message' => ['required', 'string', 'max:1000'],
        ]);

        $oldMessage = $user->admin_message;

        $user->admin_message = $request->input('admin_message');
        $user->save();

        // Audit log for setting a personal message
        AuditLogger::log(
            'admin.message.sent',
            $user,
            "Admin sent a personal message to {$user->name} (ID: {$user->id})",
            ['admin_message' => $oldMessage],
            ['admin_message' => $user->admin_message]
        );

        return redirect()->back()
            ->with('success', "Message queued for {$user->name}. They will see it on their next page load.");
    }

    public function clearMessage(User $user)
    {
        $oldMessage = $user->admin_message;

        $user->admin_message = null;
        $user->save();

        // Audit log for clearing a personal message
        AuditLogger::log(
            'admin.message.cleared',
            $user,
            "Admin cleared the personal message for {$user->name} (ID: {$user->id})",
            ['admin_message' => $oldMessage],
            ['admin_message' => null]
        );

        return redirect()->back()
            ->with('success', "Message cleared for {$user->name}.");
    }

    // ── USER DISMISSES THEIR OWN PERSONAL MESSAGE ─────────────────────────────
    public function dismiss(Request $request)
    {
        $user = $request->user();

        if ($user && $user->admin_message !== null) {
            $oldMessage = $user->admin_message;

            $user->admin_message = null;
            $user->save();

            // Log user dismissal (this is a user action, not admin)
            AuditLogger::log(
                'profile.message.dismissed',
                $user,
                "{$user->name} dismissed their personal admin message",
                ['admin_message' => $oldMessage],
                ['admin_message' => null]
            );
        }

        return redirect()->back();
    }

    // ── BROADCAST TO ALL MEMBERS ─────────────────────────────────────────────
    public function broadcast(Request $request)
    {
        $request->validate([
            'broadcast_message' => ['required', 'string', 'max:1000'],
        ]);

        $oldBroadcast = Setting::get('broadcast_message', '');
        $oldId = (int) Setting::get('broadcast_message_id', 0);

        $newMessage = $request->input('broadcast_message');
        $newId = $oldId + 1;

        Setting::set('broadcast_message', $newMessage);
        Setting::set('broadcast_message_id', $newId);

        // Audit log for broadcast
        AuditLogger::log(
            'admin.message.broadcast',
            null,  // No specific user - system-wide action
            "Admin sent a broadcast message to all members",
            [
                'broadcast_message' => $oldBroadcast,
                'broadcast_id' => $oldId,
            ],
            [
                'broadcast_message' => $newMessage,
                'broadcast_id' => $newId,
            ]
        );

        return redirect()->back()
            ->with('success', 'Broadcast message sent to all members.');
    }

    public function clearBroadcast()
    {
        $oldBroadcast = Setting::get('broadcast_message', '');
        $oldId = (int) Setting::get('broadcast_message_id', 0);

        Setting::set('broadcast_message', '');
        Setting::set('broadcast_message_id', 0);

        // Audit log for clearing broadcast
        AuditLogger::log(
            'admin.message.broadcast_cleared',
            null,
            "Admin cleared the broadcast message",
            [
                'broadcast_message' => $oldBroadcast,
                'broadcast_id' => $oldId,
            ],
            [
                'broadcast_message' => '',
                'broadcast_id' => 0,
            ]
        );

        return redirect()->back()
            ->with('success', 'Broadcast message cleared.');
    }

    // ── USER DISMISSES THE BROADCAST ─────────────────────────────────────────
    public function dismissBroadcast(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $oldDismissedId = $user->dismissed_broadcast_id;
            $newDismissedId = (int) Setting::get('broadcast_message_id', 0);

            $user->dismissed_broadcast_id = $newDismissedId;
            $user->save();

            // Log user dismissal of broadcast
            AuditLogger::log(
                'profile.broadcast.dismissed',
                $user,
                "{$user->name} dismissed the current broadcast message",
                ['dismissed_broadcast_id' => $oldDismissedId],
                ['dismissed_broadcast_id' => $newDismissedId]
            );
        }

        return redirect()->back();
    }
}