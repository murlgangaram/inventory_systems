<?php

use App\Models\Account;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:create-super-admin', function () {
    $username = trim((string) $this->ask('Super Admin username'));
    $email = trim((string) $this->ask('Super Admin email address'));

    if ($username === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('Enter a username and a valid email address.');

        return 1;
    }

    if (Account::where('username', $username)->exists()) {
        $this->error('That username is already in use.');

        return 1;
    }

    if (Account::where('email', $email)->exists()) {
        $this->error('That email address is already in use.');

        return 1;
    }

    $password = (string) $this->secret('Password (minimum 12 characters)');
    $confirmation = (string) $this->secret('Confirm password');

    if (strlen($password) < 12 || $password !== $confirmation) {
        $this->error('Passwords must match and contain at least 12 characters.');

        return 1;
    }

    Account::create([
        'employee_id' => null,
        'username' => $username,
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'super_admin',
        'status' => 'Active',
    ]);

    $this->info('Standalone Super Admin account created successfully.');

    return 0;
})->purpose('Create a standalone Super Admin account');
