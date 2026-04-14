<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\AdminNotificationRecipient;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationAdminController extends Controller
{
    public function index()
    {
        $notifications = AdminNotification::with([
            'sender',
            'recipients.user',
        ])
        ->orderByDesc('created_at')
        ->paginate(20);

        $priorityConfig = AdminNotification::priorityConfig();
        $totalUsers     = User::where('registration_pending', false)->count();

        return view('admin.notifications.index', compact(
            'notifications', 'priorityConfig', 'totalUsers'
        ));
    }

public function store(Request $request)
{
    $data = $request->validate([
        'title'      => ['required', 'string', 'max:255'],
        'body'       => ['nullable', 'string', 'max:2000'],
        'priority'   => ['required', 'integer', 'min:1', 'max:5'],
        'send_to'    => ['required', 'in:all,selected'],
        'user_ids'   => ['required_if:send_to,selected', 'array'],
        'user_ids.*' => ['exists:users,id'],
    ]);

    $notification = AdminNotification::create([
        'title'       => $data['title'],
        'body'        => $data['body'] ?? null,
        'priority'    => $data['priority'],
        'sent_by'     => auth()->id(),
        'sent_to_all' => $data['send_to'] === 'all',
    ]);

    if ($data['send_to'] === 'all') {
        $userIds = User::where('registration_pending', false)->pluck('id');
    } else {
        $userIds = collect($data['user_ids']);
    }

    $rows = $userIds->map(fn ($uid) => [
        'notification_id' => $notification->id,
        'user_id'         => $uid,
        'email_token'     => $data['priority'] >= 3
            ? bin2hex(random_bytes(32))
            : null,
        'created_at'      => now(),
        'updated_at'      => now(),
    ])->values()->all();

    AdminNotificationRecipient::insert($rows);

    // Send email for priority 3+
    if ($data['priority'] >= 3) {
        $recipients = AdminNotificationRecipient::with('user')
            ->where('notification_id', $notification->id)
            ->get();

        foreach ($recipients as $recipient) {
            if (!$recipient->user) continue;
            try {
                $recipient->user->notify(
                    new \App\Notifications\AdminNotificationEmail($notification, $recipient->email_token)
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning(
                    "Failed to send notification email to user {$recipient->user_id}: " . $e->getMessage()
                );
            }
        }
    }

    return redirect()->route('admin.notifications.index')
        ->with('status', "Notification sent to {$userIds->count()} member(s)" .
            ($data['priority'] >= 3 ? ' · Email sent to all recipients' : '') . '.');
}

    public function destroy(AdminNotification $notification)
    {
        $notification->delete();
        return redirect()->route('admin.notifications.index')
            ->with('status', 'Notification deleted.');
    }

    public function removeRecipient(AdminNotification $notification, User $user)
    {
        AdminNotificationRecipient::where('notification_id', $notification->id)
            ->where('user_id', $user->id)
            ->update(['removed_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function userSearch(Request $request)
    {
        $q = $request->get('q', '');

        $users = User::where('registration_pending', false)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('callsign', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(15)
            ->get(['id', 'name', 'email', 'callsign']);

        return response()->json($users);
    }
}