<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountControlController extends Controller
{
    // ── FORCE LOGOUT ─────────────────────────────────────────────────────────

    public function forceLogout(User $user)
    {
        $sessionCount = DB::table('sessions')->where('user_id', $user->id)->count();

        DB::table('sessions')->where('user_id', $user->id)->delete();

        AuditLogger::log(
            'user.force_logout',
            $user,
            "Force logout applied to {$user->name} — {$sessionCount} session(s) terminated"
        );

        return redirect()->back()
            ->with('success', "{$user->name} has been forcibly logged out.");
    }

    // ── FORCE PASSWORD RESET ─────────────────────────────────────────────────

    public function forcePasswordReset(User $user)
    {
        $user->force_password_reset = true;
        $user->save();

        DB::table('sessions')->where('user_id', $user->id)->delete();

        AuditLogger::log(
            'user.force_password_reset',
            $user,
            "Password reset flag set for {$user->name} — sessions killed",
            ['force_password_reset' => false],
            ['force_password_reset' => true]
        );

        return redirect()->back()
            ->with('success', "{$user->name} will be required to change their password on next login.");
    }

    public function clearPasswordReset(User $user)
    {
        $user->force_password_reset = false;
        $user->save();

        AuditLogger::log(
            'user.clear_password_reset',
            $user,
            "Password reset flag cleared for {$user->name}",
            ['force_password_reset' => true],
            ['force_password_reset' => false]
        );

        return redirect()->back()
            ->with('success', "Password reset flag cleared for {$user->name}.");
    }

    // ── SUSPEND ACCOUNT ──────────────────────────────────────────────────────

    public function suspend(User $user, Request $request)
    {
        $request->validate([
            'suspension_message' => ['nullable', 'string', 'max:500'],
        ]);

        $message = $request->input('suspension_message')
            ?: 'Your account has been suspended. Please contact an administrator.';

        $user->suspended_at       = now();
        $user->suspension_message = $message;
        $user->save();

        DB::table('sessions')->where('user_id', $user->id)->delete();

        AuditLogger::log(
            'user.suspended',
            $user,
            "Suspended {$user->name}",
            ['suspended_at' => null, 'suspension_message' => null],
            ['suspended_at' => $user->suspended_at, 'suspension_message' => $message]
        );

        return redirect()->back()
            ->with('success', "{$user->name}'s account has been suspended.");
    }

    public function unsuspend(User $user)
    {
        $suspendedAt = $user->suspended_at;

        $user->suspended_at       = null;
        $user->suspension_message = null;
        $user->save();

        AuditLogger::log(
            'user.unsuspended',
            $user,
            "Suspension lifted for {$user->name}",
            ['suspended_at' => $suspendedAt, 'suspension_message' => $user->getOriginal('suspension_message')],
            ['suspended_at' => null, 'suspension_message' => null]
        );

        return redirect()->back()
            ->with('success', "{$user->name}'s account has been reinstated.");
    }

    // ── EMAIL VERIFICATION ────────────────────────────────────────────────────

    public function markEmailVerified(User $user)
    {
        $user->markEmailAsVerified();

        AuditLogger::log(
            'user.email_verified',
            $user,
            "Email manually marked as verified for {$user->name}",
            ['email_verified_at' => null],
            ['email_verified_at' => now()->toDateTimeString()]
        );

        return back()->with('success', 'Email marked as verified.');
    }

    public function sendVerificationEmail(User $user)
    {
        $user->sendEmailVerificationNotification();

        AuditLogger::log(
            'user.verification_email_sent',
            $user,
            "Verification email sent to {$user->name} ({$user->email})"
        );

        return back()->with('success', 'Verification email sent to ' . $user->email);
    }

    // ── TERMINATE SINGLE SESSION ──────────────────────────────────────────────

    public function terminateSession(Request $request, User $user, string $sessionId)
    {
        DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->delete();

        AuditLogger::log(
            'user.session_terminated',
            $user,
            "Single session terminated for {$user->name}",
            [],
            ['session_id' => substr($sessionId, 0, 12) . '…']
        );

        return redirect()->back()
            ->with('success', 'Session terminated.')
            ->with('active_tab', 'sessions');
    }

    // ── WHO'S ONLINE ──────────────────────────────────────────────────────────

    public function online()
    {
        $cutoff = now()->subMinutes(15)->timestamp;

        $online = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->whereNotNull('sessions.user_id')
            ->where('sessions.last_activity', '>', $cutoff)
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.is_admin',
                'users.suspended_at',
                'sessions.last_activity',
                'sessions.ip_address',
                'sessions.user_agent'
            )
            ->orderByDesc('sessions.last_activity')
            ->get()
            ->unique('id');

        return view('admin.online', compact('online'));
    }
}