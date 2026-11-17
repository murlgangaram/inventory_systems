
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Edit Employee</h2>
            <p class="text-muted mb-0">Update employee information.</p>
        </div>

        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
            Back to Employees
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Employee ID *</label>
                        <input type="text" name="employee_id"
                            class="form-control @error('employee_id') is-invalid @enderror"
                            value="{{ old('employee_id', $employee->employee_id) }}" required>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name *</label>
                        <input type="text" name="full_name"
                            class="form-control @error('full_name') is-invalid @enderror"
                            value="{{ old('full_name', $employee->full_name) }}" required>
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Position</label>
                        <input type="text" name="position"
                            class="form-control @error('position') is-invalid @enderror"
                            value="{{ old('position', $employee->position) }}">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Office / Department *</label>
                        <select name="office"
                            class="form-select @error('office') is-invalid @enderror" required>
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
                                    {{ old('office', $employee->office) == $office ? 'selected' : '' }}>
                                    {{ $office }}
                                </option>
                            @endforeach
                        </select>
                        @error('office')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Job Description</label>
                        <textarea name="job_description" rows="4"
                            class="form-control @error('job_description') is-invalid @enderror">{{ old('job_description', $employee->job_description) }}</textarea>
                        @error('job_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Employment Status *</label>
                        <select name="status"
                            class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Active"
                                {{ old('status', $employee->status) == 'Active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="Inactive"
                                {{ old('status', $employee->status) == 'Inactive' ? 'selected' : '' }}>
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
                        Update Employee
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
