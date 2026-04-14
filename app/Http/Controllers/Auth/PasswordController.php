<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     *
     * Used both for:
     *  - normal "change password" from Members’ hub
     *  - forced password reset on first login / expiry
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validation rules
        $rules = [
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ];

        // Only require current password if this is a normal change
        if (! $user->force_password_reset) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules);

        // Actually update the password + security metadata
        $user->password = Hash::make($validated['password']);
        $user->password_changed_at = now();
        $user->force_password_reset = false;
        $user->save();

        return redirect()
            ->route('members')
            ->with('status', 'Password updated successfully.');
    }
}