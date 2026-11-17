
@extends('layouts.app')

@section('title', 'Dashboard | Inventory Management System')

@section('content')

<div class="container-fluid py-2">

    {{-- Dashboard Header --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Dashboard</h2>
        <p class="text-muted mb-0">
            Welcome to your Inventory Management System.
        </p>
    </div>

    {{-- Welcome Card --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-2">
                Welcome, {{ Auth::user()->username }}!
            </h5>

            <p class="text-muted mb-1">
                Employee:
                {{ Auth::user()->employee?->full_name ?? 'Not assigned' }}
            </p>

            <p class="text-muted mb-0">
                Role:
                {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}
            </p>
        </div>
    </div>

    {{-- Equipment Overview --}}
    <h5 class="fw-bold mb-3">Inventory Overview</h5>

    <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-2">Total Equipment</p>
                    <h2 class="fw-bold mb-0">{{ $totalEquipment }}</h2>
                    <small class="text-muted">Computers and printers</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-2">Computers</p>
                    <h2 class="fw-bold mb-0">{{ $computerCount }}</h2>
                    <small class="text-muted">Registered computers</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-2">Printers</p>
                    <h2 class="fw-bold mb-0">{{ $printerCount }}</h2>
                    <small class="text-muted">Registered printers</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-2">Employees</p>
                    <h2 class="fw-bold mb-0">{{ $employeeCount }}</h2>
                    <small class="text-muted">Registered employees</small>
                </div>
            </div>
        </div>

    </div>

    {{-- Equipment Status --}}
    <h5 class="fw-bold mb-3">Equipment Status</h5>

    <div class="row g-3 mb-4">

        @foreach ($statusCounts as $status => $count)

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">

                        <p class="text-muted mb-2">
                            {{ $status }}
                        </p>

                        <h3 class="fw-bold mb-0">
                            {{ $count }}
                        </h3>

                    </div>
                </div>
            </div>

        @endforeach

    </div>

    {{-- Office Distribution --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">

        <div class="card-body p-4">

            <h5 class="fw-bold mb-3">Equipment Distribution by Office</h5>

            @if ($officeCounts->isEmpty())

                <p class="text-muted mb-0">
                    No equipment records available.
                </p>

            @else

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>Office</th>
                                <th class="text-end">Total Equipment</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($officeCounts as $office => $count)

                                <tr>
                                    <td>{{ $office }}</td>

                                    <td class="text-end fw-bold">
                                        {{ $count }}
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

    {{-- Recent Activities --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="fw-bold mb-0">Recent Activities</h5>

                @if (Auth::user()->role === 'super_admin')
                    <a href="{{ route('activity-logs.index') }}"
                       class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                @endif

            </div>

            @if ($recentActivities->isEmpty())

                <p class="text-muted mb-0">
                    No recent activities recorded.
                </p>

            @else

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Account</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($recentActivities as $activity)

                                <tr>
                                    <td class="text-nowrap">
                                        {{ $activity->created_at->format('M d, Y h:i A') }}
                                    </td>

                                    <td>
                                        {{ $activity->account?->username ?? 'Deleted Account' }}
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $activity->action }}
                                        </span>
                                    </td>

                                    <td>{{ $activity->module }}</td>

                                    <td>
                                        {{ $activity->description ?? 'No description' }}
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection
