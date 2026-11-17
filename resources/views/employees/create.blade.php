
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Add Employee</h2>
            <p class="text-muted mb-0">Register a new employee in the inventory system.</p>
        </div>

        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
            Back to Employees
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="{{ route('employees.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="employee_id" class="form-label fw-semibold">
                            Employee ID <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="employee_id"
                            id="employee_id"
                            class="form-control @error('employee_id') is-invalid @enderror"
                            value="{{ old('employee_id') }}"
                            placeholder="Enter employee ID"
                            required
                        >
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="full_name" class="form-label fw-semibold">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="full_name"
                            id="full_name"
                            class="form-control @error('full_name') is-invalid @enderror"
                            value="{{ old('full_name') }}"
                            placeholder="Enter full name"
                            required
                        >
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="position" class="form-label fw-semibold">
                            Position
                        </label>
                        <input
                            type="text"
                            name="position"
                            id="position"
                            class="form-control @error('position') is-invalid @enderror"
                            value="{{ old('position') }}"
                            placeholder="Enter employee position"
                        >
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="office" class="form-label fw-semibold">
                            Office / Department <span class="text-danger">*</span>
                        </label>
                        <select
                            name="office"
                            id="office"
                            class="form-select @error('office') is-invalid @enderror"
                            required
                        >
                            <option value="">Select Office</option>
                            @foreach ([
                                'Administrative Office',
                                'Accounting Office',
                                'Assessors Office',
                                'HRMO - HR Office',
                                'IT Office',
                                'Other'
                            ] as $office)
                                <option value="{{ $office }}"
                                    {{ old('office') == $office ? 'selected' : '' }}>
                                    {{ $office }}
                                </option>
                            @endforeach
                        </select>
                        @error('office')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="job_description" class="form-label fw-semibold">
                            Job Description
                        </label>
                        <textarea
                            name="job_description"
                            id="job_description"
                            rows="4"
                            class="form-control @error('job_description') is-invalid @enderror"
                            placeholder="Enter employee job description"
                        >{{ old('job_description') }}</textarea>
                        @error('job_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">
                            Employment Status <span class="text-danger">*</span>
                        </label>
                        <select
                            name="status"
                            id="status"
                            class="form-select @error('status') is-invalid @enderror"
                            required
                        >
                            <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('employees.index') }}" class="btn btn-light border">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Save Employee
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
