<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SimpleAuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle the login attempt.
     */
    public function processLogin(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = strtolower(trim($data['login']));

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$login])
            ->orWhereRaw('LOWER(callsign) = ?', [$login])
            ->first();

        // Failed — wrong credentials or no account
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            AuditLogger::log(
                'auth.login_failed',
                $user ?? null,
                'Failed login attempt' . ($user ? " for {$user->name}" : " for unknown account: {$login}"),
                [],
                ['login' => $login, 'ip' => $request->ip(), 'reason' => $user ? 'wrong_password' : 'account_not_found']
            );

            return back()
                ->withInput($request->only('login'))
                ->withErrors([
                    'login' => 'Invalid credentials or no account found.',
                ]);
        }

        // Log them in
        Auth::login($user, $request->boolean('remember'));

        // Update password meta
        $user->force_password_reset = false;
        $user->password_changed_at  = now();
        $user->save();

        $request->session()->regenerate();

        AuditLogger::log(
            'auth.login',
            $user,
            "Successful login: {$user->name}",
            [],
            ['ip' => $request->ip(), 'remember' => $request->boolean('remember')]
        );

        return redirect()->intended(route('members'));
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditLogger::log(
                'auth.logout',
                $user,
                "Logged out: {$user->name}",
                [],
                ['ip' => $request->ip()]
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}