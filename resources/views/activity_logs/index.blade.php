
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Activity Logs</h2>
            <p class="text-muted mb-0">Monitor and review activities performed in the inventory system.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('activity-logs.index') }}" method="GET">
                <div class="row g-2">

                    <div class="col-md-7">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search username, employee, action, description..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <select name="module" class="form-select">
                            <option value="">All Modules</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module }}"
                                    {{ request('module') == $module ? 'selected' : '' }}>
                                    {{ $module }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Search
                        </button>

                        <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">System Activity History</h5>
                <span class="text-muted small">
                    {{ $logs->total() }} records found
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Date & Time</th>
                            <th>Account</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="ps-3 text-nowrap">
                                    <div class="fw-semibold">
                                        {{ $log->created_at?->format('M d, Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $log->created_at?->format('h:i A') }}
                                    </small>
                                </td>

                                <td>
                                    @if ($log->account)
                                        <div class="fw-semibold">
                                            {{ $log->account->username }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $log->account->employee?->full_name ?? 'No employee linked' }}
                                        </small>
                                    @else
                                        <span class="text-muted">System / Deleted Account</span>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $actionColor = match (strtolower($log->action)) {
                                            'created', 'login' => 'success',
                                            'updated' => 'primary',
                                            'deleted', 'logout' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $actionColor }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $log->module }}
                                    </span>
                                </td>

                                <td style="min-width: 250px;">
                                    {{ $log->description ?? 'No description provided.' }}
                                </td>

                                <td class="text-nowrap">
                                    {{ $log->ip_address ?? 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-journal-text fs-1 text-muted"></i>
                                    <h6 class="mt-2 fw-semibold">No Activity Logs Found</h6>
                                    <p class="text-muted mb-0">
                                        Activities will appear here once they are recorded.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($logs->hasPages())
            <div class="card-footer bg-white">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
