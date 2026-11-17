
@extends('layouts.app')

@section('title', 'Employee Details | Inventory System')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Employee Details</h2>
            <p class="text-muted mb-0">View employee information and assigned equipment.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('employees.edit', $employee->id) }}"
               class="btn btn-primary">
                Edit Employee
            </a>

            <a href="{{ route('employees.index') }}"
               class="btn btn-outline-secondary">
                Back
            </a>
        </div>
    </div>

    {{-- Employee Information --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Personal Information</h5>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">

                <div class="col-md-4">
                    <small class="text-muted">Employee ID</small>
                    <div class="fw-semibold">{{ $employee->employee_id }}</div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Full Name</small>
                    <div class="fw-semibold">{{ $employee->full_name }}</div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Position</small>
                    <div class="fw-semibold">{{ $employee->position ?: 'Not specified' }}</div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Office / Department</small>
                    <div class="fw-semibold">{{ $employee->office }}</div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Employment Status</small>
                    <div>
                        @if($employee->status === 'Active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Job Description</small>
                    <div class="fw-semibold">
                        {{ $employee->job_description ?: 'Not specified' }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Account Information --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Account Information</h5>
        </div>

        <div class="card-body p-4">
            @if($employee->account)
                <div class="row g-4">

                    <div class="col-md-4">
                        <small class="text-muted">Username</small>
                        <div class="fw-semibold">{{ $employee->account->username }}</div>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Email</small>
                        <div class="fw-semibold">{{ $employee->account->email }}</div>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Role</small>
                        <div>
                            <span class="badge bg-primary">
                                {{ ucfirst(str_replace('_', ' ', $employee->account->role)) }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Account Status</small>
                        <div>
                            @if($employee->account->status === 'Active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">
                                    {{ $employee->account->status }}
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
            @else
                <p class="text-muted mb-0">
                    No account has been created for this employee.
                </p>
            @endif
        </div>
    </div>

    {{-- Assigned Computers --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                Assigned Computers
                <span class="badge bg-primary">
                    {{ $employee->computers->count() }}
                </span>
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Computer Name</th>
                            <th>Office</th>
                            <th>Brand / Model</th>
                            <th>Serial Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($employee->computers as $computer)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $computer->computer_name }}
                                </td>
                                <td>{{ $computer->office }}</td>
                                <td>
                                    {{ $computer->brand }} {{ $computer->model }}
                                </td>
                                <td>{{ $computer->serial_number ?: 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $computer->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No computers assigned to this employee.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Assigned Printers --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                Assigned Printers
                <span class="badge bg-primary">
                    {{ $employee->printers->count() }}
                </span>
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Printer Name</th>
                            <th>Brand / Model</th>
                            <th>Office</th>
                            <th>Serial Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($employee->printers as $printer)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $printer->printer_name }}
                                </td>
                                <td>{{ $printer->brand_model }}</td>
                                <td>{{ $printer->office }}</td>
                                <td>{{ $printer->serial_number ?: 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $printer->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No printers assigned to this employee.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
