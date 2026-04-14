<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class MagicCodeController extends Controller
{
    /**
     * Step 1 — find the account, generate a code, send it.
     */
    public function request(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string', 'max:255'],
        ]);

        $login = trim($request->login);

        $user = User::where('email', $login)
            ->orWhere('callsign', strtoupper($login))
            ->first();

        if (! $user) {
            AuditLogger::log(
                'auth.magic_code_failed',
                null,
                "Magic code requested for unknown account: {$login}",
                [],
                ['login' => $login, 'ip' => $request->ip(), 'reason' => 'account_not_found']
            );

            return response()->json([
                'success' => false,
                'message' => 'No account found with that email or callsign.',
            ], 404);
        }

        if ($user->suspended_at) {
            AuditLogger::log(
                'auth.magic_code_blocked',
                $user,
                "Magic code blocked — account suspended: {$user->name}",
                [],
                ['reason' => 'suspended', 'ip' => $request->ip()]
            );

            return response()->json([
                'success' => false,
                'message' => 'This account has been suspended. Please contact an administrator.',
            ], 403);
        }

        if ($user->registration_pending) {
            AuditLogger::log(
                'auth.magic_code_blocked',
                $user,
                "Magic code blocked — registration pending: {$user->name}",
                [],
                ['reason' => 'registration_pending', 'ip' => $request->ip()]
            );

            return response()->json([
                'success' => false,
                'message' => 'Your account is still awaiting approval.',
            ], 403);
        }

        // Generate a 6-digit code and store it hashed for 10 minutes
        $code     = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = 'magic_code_' . $user->id;
        Cache::put($cacheKey, Hash::make($code), now()->addMinutes(10));

        // Send the email
        Mail::send('emails.magic-code', [
            'user' => $user,
            'code' => $code,
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your ' . \App\Helpers\RaynetSetting::groupName() . ' sign-in code');
        });

        AuditLogger::log(
            'auth.magic_code_sent',
            $user,
            "Magic code sent to {$user->name} ({$user->email})",
            [],
            ['ip' => $request->ip()]
        );

        // Mask the email for display: j***@example.com
        $parts  = explode('@', $user->email);
        $masked = substr($parts[0], 0, 1) . str_repeat('*', max(strlen($parts[0]) - 1, 3)) . '@' . $parts[1];

        return response()->json([
            'success' => true,
            'sent_to' => $masked,
        ]);
    }

    /**
     * Step 2 — verify the code and log the user in.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'code'  => ['required', 'string', 'size:6'],
        ]);

        $login = trim($request->login);
        $code  = trim($request->code);

        $user = User::where('email', $login)
            ->orWhere('callsign', strtoupper($login))
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.',
            ], 404);
        }

        $cacheKey = 'magic_code_' . $user->id;
        $stored   = Cache::get($cacheKey);

        if (! $stored || ! Hash::check($code, $stored)) {
            AuditLogger::log(
                'auth.magic_code_invalid',
                $user,
                "Invalid or expired magic code attempt for {$user->name}",
                [],
                ['ip' => $request->ip(), 'reason' => $stored ? 'wrong_code' : 'expired']
            );

            return response()->json([
                'success' => false,
                'message' => 'Incorrect or expired code. Please request a new one.',
            ], 422);
        }

        // Code is valid — consume it and log in
        Cache::forget($cacheKey);
        Auth::login($user, false);
        $request->session()->regenerate();

        AuditLogger::log(
            'auth.magic_code_login',
            $user,
            "Successful magic code login: {$user->name}",
            [],
            ['ip' => $request->ip()]
        );

        return response()->json([
            'success'  => true,
            'redirect' => route('members'),
        ]);
    }
}