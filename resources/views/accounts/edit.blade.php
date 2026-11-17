
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Edit Account</h2>
            <p class="text-muted mb-0">Update account information and access permissions.</p>
        </div>

        <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Accounts
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="{{ route('accounts.update', $account->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="employee_id" class="form-label fw-semibold">Employee</label>
                        <select name="employee_id" id="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ old('employee_id', $account->employee_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text"
                               name="username"
                               id="username"
                               class="form-control"
                               value="{{ old('username', $account->username) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email"
                               name="email"
                               id="email"
                               class="form-control"
                               value="{{ old('email', $account->email) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label fw-semibold">Account Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="super_admin" {{ old('role', $account->role) == 'super_admin' ? 'selected' : '' }}>
                                Super Admin
                            </option>
                            <option value="admin" {{ old('role', $account->role) == 'admin' ? 'selected' : '' }}>
                                Admin
                            </option>
                            <option value="user" {{ old('role', $account->role) == 'user' ? 'selected' : '' }}>
                                Staff
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">Account Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="Active" {{ old('status', $account->status) == 'Active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="Disabled" {{ old('status', $account->status) == 'Disabled' ? 'selected' : '' }}>
                                Disabled
                            </option>
                        </select>
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <h5 class="fw-bold mb-1">Change Password</h5>
                        <p class="text-muted small mb-2">
                            Leave these fields blank if you do not want to change the current password.
                        </p>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold">New Password</label>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control"
                               autocomplete="new-password">
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control"
                               autocomplete="new-password">
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('accounts.index') }}" class="btn btn-light">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
