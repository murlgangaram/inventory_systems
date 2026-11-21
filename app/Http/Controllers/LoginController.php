<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::lower($request->input('username')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'username' => 'Too many login attempts. Please try again later.',
            ]);
        }

        $credentials['status'] = 'Active';

        // Super Admin accounts may operate independently of an employee record.
        // Admin and Staff accounts must still belong to an active employee.
        $accountCanLogin = Account::where('username', $credentials['username'])
            ->where('status', 'Active')
            ->where(function ($query) {
                $query->where('role', 'super_admin')
                    ->orWhereHas('employee', function ($employeeQuery) {
                        $employeeQuery->where('status', 'Active');
                    });
            })
            ->exists();

        if (
            !$accountCanLogin ||
            !Auth::attempt($credentials, $request->boolean('remember'))
        ) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'username' => 'Invalid username or password, or your account is disabled.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
