<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Employee;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * Display all accounts.
     */
    public function index()
    {
        $accounts = Account::with('employee')
            ->latest()
            ->paginate(10);

        return view('accounts.index', compact('accounts'));
    }

    /**
     * Show the create account form.
     */
    public function create()
    {
        $employees = Employee::whereDoesntHave('account')
            ->orderBy('full_name')
            ->get();

        return view('accounts.create', compact('employees'));
    }

    /**
     * Store a new account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
                'unique:accounts,employee_id',
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:accounts,username',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:accounts,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => [
                'required',
                Rule::in(['super_admin', 'admin', 'user']),
            ],
            'status' => [
                'required',
                Rule::in(['Active', 'Disabled']),
            ],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $account = Account::create($validated);

        // Record account creation.
        ActivityLogger::log(
            'Created',
            'Accounts',
            "Created account for username: {$account->username}. Role: {$account->role}.",
            $account->id
        );

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    /**
     * Show the edit account form.
     */
    public function edit(Account $account)
    {
        $employees = Employee::whereDoesntHave('account')
            ->orWhere('id', $account->employee_id)
            ->orderBy('full_name')
            ->get();

        return view('accounts.edit', compact('account', 'employees'));
    }

    /**
     * Update an existing account.
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
                Rule::unique('accounts', 'employee_id')
                    ->ignore($account->id),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('accounts', 'username')
                    ->ignore($account->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('accounts', 'email')
                    ->ignore($account->id),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => [
                'required',
                Rule::in(['super_admin', 'admin', 'user']),
            ],
            'status' => [
                'required',
                Rule::in(['Active', 'Disabled']),
            ],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $account->update($validated);

        // Record account update.
        ActivityLogger::log(
            'Updated',
            'Accounts',
            "Updated account for username: {$account->username}. Role: {$account->role}.",
            $account->id
        );

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Account updated successfully.');
    }
}
