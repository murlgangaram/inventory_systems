
@extends('layouts.app')

@section('title', 'Account Management')

@section('content')

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3>Account Management</h3>
        <p class="text-muted mb-0">
            Manage system accounts, usernames, and user roles.
        </p>
    </div>

    <a href="{{ route('accounts.create') }}" class="btn btn-primary">
        + Create Account
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="content-card bg-white border-0 rounded-3 p-4 shadow-sm">

    <h5 class="mb-3">System Accounts</h5>

    <div class="table-responsive">
        <table class="table table-hover">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($accounts as $account)
                    <tr>
                        <td>
                            {{ $account->employee?->full_name ?? 'No Employee Linked' }}
                        </td>

                        <td>{{ $account->username }}</td>

                        <td>{{ $account->email }}</td>

                        <td>
                            @if($account->role === 'super_admin')
                                <span class="badge text-bg-danger">
                                    Super Admin
                                </span>
                            @elseif($account->role === 'admin')
                                <span class="badge text-bg-primary">
                                    Admin
                                </span>
                            @else
                                <span class="badge text-bg-secondary">
                                    Staff
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($account->status === 'Active')
                                <span class="badge text-bg-success">
                                    Active
                                </span>
                            @else
                                <span class="badge text-bg-secondary">
                                    Disabled
                                </span>
                            @endif
                        </td>

                        <td class="text-nowrap">
                            <a href="{{ route('accounts.edit', $account->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No accounts found.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <div class="mt-3">
        {{ $accounts->links() }}
    </div>

</div>

@endsection
