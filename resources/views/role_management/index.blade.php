@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Role Management</h2>
            <p class="text-muted mb-0">
                Review system roles and their assigned permissions.
            </p>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Note:</strong> Permissions are enforced by the system's role middleware.
        This page is a read-only overview of the current access levels.
    </div>

    <div class="row g-4">
        @foreach ($roles as $role)
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-1">
                            {{ $role['name'] }}
                        </h5>

                        <span class="badge bg-primary mb-3">
                            {{ $role['role'] }}
                        </span>

                        <p class="text-muted small">
                            {{ $role['description'] }}
                        </p>

                        <hr>

                        <h6 class="fw-bold mb-3">Assigned Permissions</h6>

                        <ul class="list-unstyled mb-0">
                            @foreach ($role['permissions'] as $permission)
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    {{ $permission }}
                                </li>
                            @endforeach
                        </ul>

                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
