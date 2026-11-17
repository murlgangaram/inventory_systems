
@extends('layouts.app')

@section('title', 'Employee Profile | Inventory System')

@section('content')

<style>
    .profile-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0,0,0,.05);
    }

    .profile-label {
        color: #64748b;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .profile-value {
        font-weight: 600;
        color: #1e293b;
        word-break: break-word;
    }

    .summary-card {
        border: none;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 15px rgba(0,0,0,.04);
    }

    .summary-number {
        font-size: 28px;
        font-weight: 700;
    }

    .table th {
        white-space: nowrap;
        font-size: 13px;
        color: #475569;
    }

    .table td {
        font-size: 13px;
        vertical-align: middle;
    }

    @media print {
        .no-print,
        .sidebar,
        nav {
            display: none !important;
        }

        .main-content,
        main {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            background: white !important;
        }

        .profile-card,
        .summary-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            break-inside: avoid;
        }

        .table {
            width: 100%;
        }
    }
</style>

<div class="container-fluid py-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h2 class="fw-bold mb-1">Employee Profile</h2>
            <p class="text-muted mb-0">
                Complete employee information and assigned equipment.
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap no-print">

            <button onclick="window.print()" class="btn btn-outline-dark">
                Print Profile
            </button>

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

    {{-- EMPLOYEE SUMMARY --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="summary-card bg-white">
                <div class="text-muted small">Assigned Computers</div>
                <div class="summary-number text-primary">
                    {{ $employee->computers->count() }}
                </div>
                <small class="text-muted">Total computers assigned</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card bg-white">
                <div class="text-muted small">Assigned Printers</div>
                <div class="summary-number text-success">
                    {{ $employee->printers->count() }}
                </div>
                <small class="text-muted">Total printers assigned</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card bg-white">
                <div class="text-muted small">Total Assigned Equipment</div>
                <div class="summary-number text-dark">
                    {{ $employee->computers->count() + $employee->printers->count() }}
                </div>
                <small class="text-muted">Computers and printers combined</small>
            </div>
        </div>

    </div>

    {{-- PERSONAL INFORMATION --}}
    <div class="card profile-card mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Employee Information</h5>
        </div>

        <div class="card-body p-4">

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="profile-label">Employee ID</div>
                    <div class="profile-value">
                        {{ $employee->employee_id }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="profile-label">Full Name</div>
                    <div class="profile-value">
                        {{ $employee->full_name }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="profile-label">Position</div>
                    <div class="profile-value">
                        {{ $employee->position ?: 'Not specified' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="profile-label">Office / Department</div>
                    <div class="profile-value">
                        {{ $employee->office }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="profile-label">Employment Status</div>

                    @if($employee->status === 'Active')
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif

                </div>

                <div class="col-md-4">
                    <div class="profile-label">Registration Date</div>
                    <div class="profile-value">
                        {{ $employee->created_at?->format('M d, Y h:i A') ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="profile-label">Job Description</div>
                    <div class="profile-value">
                        {{ $employee->job_description ?: 'Not specified' }}
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="profile-label">Last Updated</div>
                    <div class="profile-value">
                        {{ $employee->updated_at?->format('M d, Y h:i A') ?? 'N/A' }}
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- ACCOUNT INFORMATION --}}
    <div class="card profile-card mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Account Information</h5>
        </div>

        <div class="card-body p-4">

            @if($employee->account)

                <div class="row g-4">

                    <div class="col-md-4">
                        <div class="profile-label">Username</div>
                        <div class="profile-value">
                            {{ $employee->account->username }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="profile-label">Email Address</div>
                        <div class="profile-value">
                            {{ $employee->account->email }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="profile-label">Account Role</div>
                        <span class="badge bg-primary">
                            {{ ucfirst(str_replace('_', ' ', $employee->account->role)) }}
                        </span>
                    </div>

                    <div class="col-md-4">
                        <div class="profile-label">Account Status</div>

                        @if($employee->account->status === 'Active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">
                                {{ $employee->account->status ?? 'Unknown' }}
                            </span>
                        @endif

                    </div>

                </div>

            @else

                <div class="alert alert-light border mb-0">
                    <i class="text-muted">No system account has been created for this employee.</i>
                </div>

            @endif

        </div>
    </div>

    {{-- ASSIGNED COMPUTERS --}}
    <div class="card profile-card mb-4">

        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">

            <h5 class="fw-bold mb-0">Assigned Computers</h5>

            <span class="badge bg-primary">
                {{ $employee->computers->count() }} Computers
            </span>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Computer Name</th>
                            <th>Office</th>
                            <th>RAM</th>
                            <th>CPU</th>
                            <th>Storage</th>
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

                                <td>{{ $computer->ram ?: 'N/A' }}</td>

                                <td>{{ $computer->cpu ?: 'N/A' }}</td>

                                <td>
                                    SSD: {{ $computer->ssd ?: 'N/A' }}
                                    <br>
                                    HDD: {{ $computer->hd ?: 'N/A' }}
                                </td>

                                <td>
                                    {{ $computer->brand ?? '' }}
                                    {{ $computer->model ?? '' }}
                                </td>

                                <td>
                                    {{ $computer->serial_number ?: 'N/A' }}
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $computer->status }}
                                    </span>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8"
                                    class="text-center text-muted py-4">
                                    No computers assigned to this employee.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

    {{-- ASSIGNED PRINTERS --}}
    <div class="card profile-card mb-4">

        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">

            <h5 class="fw-bold mb-0">Assigned Printers</h5>

            <span class="badge bg-success">
                {{ $employee->printers->count() }} Printers
            </span>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

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

                                <td>{{ $printer->brand_model ?: 'N/A' }}</td>

                                <td>{{ $printer->office }}</td>

                                <td>
                                    {{ $printer->serial_number ?: 'N/A' }}
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $printer->status }}
                                    </span>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5"
                                    class="text-center text-muted py-4">
                                    No printers assigned to this employee.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

{{-- EQUIPMENT HISTORY --}}
<div class="card profile-card mb-4">

    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Equipment Assignment History</h5>

        <span class="badge bg-dark">
            {{ $employee->equipmentAssignments->count() }} Records
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">

            <table class="table table-hover mb-0">

                <thead class="table-light">
                    <tr>
                        <th>Equipment Name</th>
                        <th>Type</th>
                        <th>Brand / Model</th>
                        <th>Serial Number</th>
                        <th>Office</th>
                        <th>Assigned Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($employee->equipmentAssignments as $assignment)

                        @php
                            $equipment = $assignment->equipment_type === 'computer'
                                ? $assignment->computer
                                : $assignment->printer;

                            $isComputer = $assignment->equipment_type === 'computer';

                            $equipmentName = $isComputer
                                ? ($equipment?->computer_name ?? 'Equipment no longer available')
                                : ($equipment?->printer_name ?? 'Equipment no longer available');

                            $brandModel = $isComputer
                                ? trim(($equipment?->brand ?? '') . ' ' . ($equipment?->model ?? ''))
                                : ($equipment?->brand_model ?? '');
                        @endphp

                        <tr>
                            <td class="fw-semibold">
                                {{ $equipmentName }}
                            </td>

                            <td>
                                <span class="badge {{ $isComputer ? 'bg-primary' : 'bg-success' }}">
                                    {{ ucfirst($assignment->equipment_type) }}
                                </span>
                            </td>

                            <td>{{ $brandModel ?: 'N/A' }}</td>

                            <td>{{ $equipment?->serial_number ?: 'N/A' }}</td>

                            <td>{{ $equipment?->office ?: 'N/A' }}</td>

                            <td>
                                {{ $assignment->assigned_at?->format('M d, Y h:i A') ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $assignment->returned_at?->format('M d, Y h:i A') ?? 'Not returned' }}
                            </td>

                            <td>
                                @if($assignment->returned_at === null)
                                    <span class="badge bg-success">Currently Assigned</span>
                                @else
                                    <span class="badge bg-secondary">Returned</span>
                                @endif
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No equipment assignment history found for this employee.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>
</div>



       

@endsection
