<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Anyone can hit the login form.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * We validate `login` (email or callsign) plus `password`.
     */
    public function rules(): array
    {
        return [
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt authentication using email OR callsign.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login    = trim($this->input('login'));
        $password = $this->input('password');

        $credentials = [
            'password' => $password,
        ];

        if (str_contains($login, '@')) {
            // Treat as email address
            $credentials['email'] = $login;
        } else {
            // Treat as callsign (case-insensitive)
            $upper = Str::upper($login);

            $user = User::whereRaw('UPPER(callsign) = ?', [$upper])->first();

            if (! $user) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'login' => trans('auth.failed'),
                ]);
            }

            // Auth::attempt still wants an email/password pair
            $credentials['email'] = $user->email;
        }

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Same as the default Breeze logic, but keyed on `login`.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Use the login value (email or callsign) + IP as the throttle key.
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('login')) . '|' . $this->ip();
    }
}