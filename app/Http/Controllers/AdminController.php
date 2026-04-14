<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        // If already logged in AND is an admin → skip login page
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');   // ✅ FIXED
        }

        return view('admin.login');
    }

    /**
     * Handle the admin login POST.
     */
    public function login(Request $request)
    {
        // 1) Validate input
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login    = trim($request->input('login'));
        $password = $request->input('password');

        // 2) Identify whether email or callsign
        if (str_contains($login, '@')) {
            $user = User::where('email', $login)->first();
        } else {
            $user = User::whereRaw('UPPER(callsign) = ?', [Str::upper($login)])->first();
        }

        // 3) No such user
        if (! $user) {
            return back()
                ->withErrors([
                    'login' => 'No matching admin account was found for that email or callsign.',
                ])
                ->withInput($request->only('login'));
        }

        // 4) User exists but is NOT an admin
        if (! $user->is_admin) {
            return back()
                ->withErrors([
                    'login' => 'This account does not have admin access.',
                ])
                ->withInput($request->only('login'));
        }

        // 5) Validate password
        if (! Hash::check($password, $user->password)) {
            return back()
                ->withErrors([
                    'login' => 'Incorrect password – please try again.',
                ])
                ->withInput($request->only('login'));
        }

        // 6) Successful login
        Auth::login($user);
        $request->session()->regenerate();

        session()->flash('status', 'Admin login OK.');

        // 7) Redirect to admin dashboard
        return redirect()->route('admin.dashboard');   // ✅ FIXED
    }

    /**
     * Admin logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}