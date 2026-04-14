<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();
        $forceReset = (bool) $user->force_password_reset;

        $rules = [
            'password'              => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ];

        if (! $forceReset) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules);

        $user->update([
            'password'             => bcrypt($request->password),
            'force_password_reset' => 0,
            'password_changed_at'  => now(),
        ]);

        return redirect()->route('members')
            ->with('status', 'Password updated successfully.');
    }
}