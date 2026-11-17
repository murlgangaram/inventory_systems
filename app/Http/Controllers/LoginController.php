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
    /**
     * Display the login page.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Process login authentication.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::lower($request->input('username'))
            . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'username' => 'Too many login attempts. Please try again later.',
            ]);
        }

       $credentials['status'] = 'Active';

// Check whether the account belongs to an active employee.
$employeeIsActive = Account::where('username', $credentials['username'])
    ->whereHas('employee', function ($query) {
        $query->where('status', 'Active');
    })
    ->exists();

if (
    !$employeeIsActive ||
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

    /**
     * Logout authenticated account.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
