
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Equipment | Inventory System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f3f6fb;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            background: #172033;
            min-height: 100vh;
            color: white;
            padding: 25px 18px;
        }

        .sidebar a {
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: white;
        }

        .main-content {
            padding: 30px;
        }

        .content-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,.04);
        }

        .form-label {
            font-weight: 600;
            color: #334155;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <aside class="col-md-3 col-lg-2 sidebar">
            <h5 class="mb-4">Inventory System</h5>

            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('equipment.index') }}" class="active">Equipment Record</a>
            <a href="#">Assigned Employees</a>
            <a href="#">Account Management</a>
            <a href="#">Role Management</a>
            <a href="#">Activity Logs</a>
        </aside>

        <main class="col-md-9 col-lg-10 main-content">

            <div class="mb-4">
                <h3>Edit {{ ucfirst($type) }}</h3>
                <p class="text-muted">Update equipment information.</p>
            </div>

            <div class="content-card">

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('equipment.update', ['type' => $type, 'id' => $equipment->id]) }}"
                      method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        @if($type === 'computer')

                            <div class="col-12">
                                <h5>Computer Information</h5>
                                <hr>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Computer Name *</label>
                                <input type="text" name="computer_name"
                                       value="{{ old('computer_name', $equipment->computer_name) }}"
                                       class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">RAM</label>
                                <input type="text" name="ram"
                                       value="{{ old('ram', $equipment->ram) }}"
                                       class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">CPU</label>
                                <input type="text" name="cpu"
                                       value="{{ old('cpu', $equipment->cpu) }}"
                                       class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SSD</label>
                                <input type="text" name="ssd"
                                       value="{{ old('ssd', $equipment->ssd) }}"
                                       class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">HDD</label>
                                <input type="text" name="hd"
                                       value="{{ old('hd', $equipment->hd) }}"
                                       class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <input type="text" name="brand"
                                       value="{{ old('brand', $equipment->brand) }}"
                                       class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Model</label>
                                <input type="text" name="model"
                                       value="{{ old('model', $equipment->model) }}"
                                       class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">MAC Address</label>
                                <input type="text" name="mac_address"
                                       value="{{ old('mac_address', $equipment->mac_address) }}"
                                       class="form-control">
                            </div>

                        @elseif($type === 'printer')

                            <div class="col-12">
                                <h5>Printer Information</h5>
                                <hr>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Printer Name *</label>
                                <input type="text" name="printer_name"
                                       value="{{ old('printer_name', $equipment->printer_name) }}"
                                       class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Brand / Model</label>
                                <input type="text" name="brand_model"
                                       value="{{ old('brand_model', $equipment->brand_model) }}"
                                       class="form-control">
                            </div>

                        @endif

                        <div class="col-md-6">
                            <label class="form-label">Serial Number</label>
                            <input type="text" name="serial_number"
                                   value="{{ old('serial_number', $equipment->serial_number) }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Office *</label>
                            <select name="office" class="form-select" required>
                                <option value="">Select Office</option>

                                @foreach([
                                    'Administrative Office',
                                    'Accounting Office',
                                    'Assessors Office',
                                    'HRMO - HR Office',
                                    'IT Office',
                                    'Other'
                                ] as $office)
                                    <option value="{{ $office }}"
                                        {{ old('office', $equipment->office) == $office ? 'selected' : '' }}>
                                        {{ $office }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Assigned Employee</label>

                            <select name="employee_id" class="form-select">
                                <option value="">Unassigned</option>

                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ old('employee_id', $equipment->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }} ({{ $employee->employee_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status *</label>

                            <select name="status" class="form-select" required>
                                @foreach([
                                    'Working',
                                    'For Maintenance',
                                    'Retired',
                                    'For Checking'
                                ] as $status)
                                    <option value="{{ $status }}"
                                        {{ old('status', $equipment->status) == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $equipment->remarks) }}</textarea>
                        </div>

                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Update Equipment
                        </button>

                        <a href="{{ route('equipment.index') }}"
                           class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>

        </main>
    </div>
</div>

</body>
</html>
