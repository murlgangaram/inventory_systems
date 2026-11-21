@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="mb-4">
    <h3>Create Account</h3>
    <p class="text-muted mb-0">
        Create a login account. Super Admin accounts do not need an employee record.
    </p>
</div>

<div class="content-card bg-white border-0 rounded-3 p-4 shadow-sm">
    <form action="{{ route('accounts.store') }}" method="POST">
        @csrf

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Account Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Staff</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select">
                    <option value="">No employee linked (Super Admin only)</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->employee_id }} - {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Required for Admin and Staff accounts. Optional for Super Admin.</small>
                @if($employees->isEmpty())
                    <small class="d-block text-muted">No unlinked employees are currently available.</small>
                @endif
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" minlength="8" autocomplete="new-password" required>
                <small class="text-muted">Minimum of 8 characters.</small>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" minlength="8" autocomplete="new-password" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Account Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Disabled" {{ old('status') == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                </select>
            </div>
        </div>

        <hr>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Create Account</button>
            <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
