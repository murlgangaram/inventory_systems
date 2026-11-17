
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Records | Inventory System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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

        .sidebar a.active,
        .sidebar a:hover {
            background: #2563eb;
            color: white;
        }

        .main-content {
            padding: 30px;
        }

        .content-card {
            background: white;
            border: none;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 3px 15px rgba(0,0,0,.04);
        }

        .filter-card {
            background: white;
            border: none;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 3px 15px rgba(0,0,0,.04);
        }

        .table th {
            white-space: nowrap;
            color: #475569;
            font-size: 13px;
        }

        .table td {
            vertical-align: middle;
            font-size: 13px;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 7px;
        }

        .result-count {
            font-size: 13px;
            color: #64748b;
        }

        @media (max-width: 767px) {
            .sidebar {
                min-height: auto;
            }

            .main-content {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <aside class="col-md-3 col-lg-2 sidebar">
            <h5 class="mb-4">Inventory System</h5>

            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('equipment.index') }}" class="active">Equipment Record</a>
            <a href="#">Assigned Employees</a>
            <a href="#">Account Management</a>
            <a href="#">Role Management</a>
            <a href="#">Activity Logs</a>
        </aside>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 main-content">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h3>Equipment Records</h3>
                    <p class="text-muted mb-0">
                        Manage computers and printers.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">

    <a href="{{ route('equipment.export.csv', request()->query()) }}"
       class="btn btn-success">
        <i class="bi bi-file-earmark-spreadsheet"></i>
        Export CSV
    </a>

    <a href="{{ route('equipment.export.pdf', request()->query()) }}"
       class="btn btn-danger">
        <i class="bi bi-file-earmark-pdf"></i>
        Export PDF
    </a>

    <a href="{{ route('equipment.print', request()->query()) }}"
       class="btn btn-outline-dark"
       target="_blank">
        <i class="bi bi-printer"></i>
        Print
    </a>

    <a href="{{ route('equipment.create') }}" class="btn btn-primary">
        + Add Equipment
    </a>

</div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Advanced Search and Filters -->
            <div class="filter-card mb-4">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="mb-1">Advanced Search & Filters</h5>
                        <p class="text-muted small mb-0">
                            Search and filter your equipment records.
                        </p>
                    </div>

                    <a href="{{ route('equipment.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        Clear Filters
                    </a>
                </div>

                <form action="{{ route('equipment.index') }}" method="GET">

                    <!-- Search Bar -->
                    <div class="mb-3">
                        <label class="filter-label">Search Equipment</label>

                        <div class="input-group">
                            <input type="search"
                                   name="search"
                                   class="form-control"
                                   placeholder="Search name, serial number, brand, model, MAC address or employee..."
                                   value="{{ $filters['search'] ?? '' }}">

                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row g-3">

                        <!-- Equipment Type -->
                        <div class="col-md-4 col-lg-3">
                            <label class="filter-label">Equipment Type</label>

                            <select name="type" class="form-select">
                                <option value="all"
                                    @selected(($filters['type'] ?? 'all') === 'all')>
                                    All Equipment
                                </option>

                                <option value="computer"
                                    @selected(($filters['type'] ?? 'all') === 'computer')>
                                    Computers
                                </option>

                                <option value="printer"
                                    @selected(($filters['type'] ?? 'all') === 'printer')>
                                    Printers
                                </option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-md-4 col-lg-3">
                            <label class="filter-label">Equipment Status</label>

                            <select name="status" class="form-select">
                                <option value="all"
                                    @selected(($filters['status'] ?? 'all') === 'all')>
                                    All Statuses
                                </option>

                                <option value="Working"
                                    @selected(($filters['status'] ?? 'all') === 'Working')>
                                    Working
                                </option>

                                <option value="For Maintenance"
                                    @selected(($filters['status'] ?? 'all') === 'For Maintenance')>
                                    For Maintenance
                                </option>

                                <option value="For Checking"
                                    @selected(($filters['status'] ?? 'all') === 'For Checking')>
                                    For Checking
                                </option>

                                <option value="Retired"
                                    @selected(($filters['status'] ?? 'all') === 'Retired')>
                                    Retired
                                </option>
                            </select>
                        </div>

                        <!-- Office -->
                        <div class="col-md-4 col-lg-3">
                            <label class="filter-label">Office / Department</label>

                            <select name="office" class="form-select">
                                <option value="all"
                                    @selected(($filters['office'] ?? 'all') === 'all')>
                                    All Offices
                                </option>

                                @foreach($offices as $office)
                                    <option value="{{ $office }}"
                                        @selected(($filters['office'] ?? 'all') === $office)>
                                        {{ $office }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assigned Employee -->
                        <div class="col-md-4 col-lg-3">
                            <label class="filter-label">Assigned Employee</label>

                            <select name="employee_id" class="form-select">
                                <option value="all"
                                    @selected(($filters['employee_id'] ?? 'all') === 'all')>
                                    All Employees
                                </option>

                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        @selected((string)($filters['employee_id'] ?? 'all') === (string)$employee->id)>
                                        {{ $employee->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assignment Status -->
                        <div class="col-md-4 col-lg-3">
                            <label class="filter-label">Assignment Status</label>

                            <select name="assignment" class="form-select">
                                <option value="all"
                                    @selected(($filters['assignment'] ?? 'all') === 'all')>
                                    All Equipment
                                </option>

                                <option value="assigned"
                                    @selected(($filters['assignment'] ?? 'all') === 'assigned')>
                                    Assigned
                                </option>

                                <option value="unassigned"
                                    @selected(($filters['assignment'] ?? 'all') === 'unassigned')>
                                    Unassigned
                                </option>
                            </select>
                        </div>

                        <!-- Sorting -->
                        <div class="col-md-4 col-lg-3">
                            <label class="filter-label">Sort By</label>

                            <select name="sort" class="form-select">
                                <option value="newest"
                                    @selected(($filters['sort'] ?? 'newest') === 'newest')>
                                    Newest First
                                </option>

                                <option value="oldest"
                                    @selected(($filters['sort'] ?? 'newest') === 'oldest')>
                                    Oldest First
                                </option>

                                <option value="name_asc"
                                    @selected(($filters['sort'] ?? 'newest') === 'name_asc')>
                                    Name (A - Z)
                                </option>

                                <option value="name_desc"
                                    @selected(($filters['sort'] ?? 'newest') === 'name_desc')>
                                    Name (Z - A)
                                </option>

                                <option value="status_asc"
                                    @selected(($filters['sort'] ?? 'newest') === 'status_asc')>
                                    Status (A - Z)
                                </option>

                                <option value="status_desc"
                                    @selected(($filters['sort'] ?? 'newest') === 'status_desc')>
                                    Status (Z - A)
                                </option>
                            </select>
                        </div>

                        <!-- Apply Filters -->
                        <div class="col-md-4 col-lg-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                Apply Filters
                            </button>
                        </div>

                    </div>
                </form>
            </div>

            <!-- Result Summary -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                <h6 class="mb-0">Search Results</h6>

                <div class="result-count">
                    Computers: <strong>{{ $computers->count() }}</strong>
                    &nbsp; | &nbsp;
                    Printers: <strong>{{ $printers->count() }}</strong>
                    &nbsp; | &nbsp;
                    Total: <strong>{{ $computers->count() + $printers->count() }}</strong>
                </div>

            </div>

            <!-- Computers -->
            @if(($filters['type'] ?? 'all') !== 'printer')

                <div class="content-card mb-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Computer Inventory</h5>

                        <span class="badge text-bg-primary">
                            {{ $computers->count() }} Records
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">

                            <thead>
                                <tr>
                                    <th>Computer Name</th>
                                    <th>Office</th>
                                    <th>RAM</th>
                                    <th>CPU</th>
                                    <th>Storage</th>
                                    <th>Serial Number</th>
                                    <th>Assigned Employee</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($computers as $computer)

                                    <tr>
                                        <td>{{ $computer->computer_name }}</td>

                                        <td>{{ $computer->office }}</td>

                                        <td>{{ $computer->ram ?? '-' }}</td>

                                        <td>{{ $computer->cpu ?? '-' }}</td>

                                        <td>
                                            {{ $computer->ssd ?? '-' }}

                                            @if($computer->hd)
                                                / {{ $computer->hd }}
                                            @endif
                                        </td>

                                        <td>{{ $computer->serial_number ?? '-' }}</td>

                                        <td>
                                            {{ $computer->employee?->full_name ?? 'Unassigned' }}
                                        </td>

                                        <td>
                                            @php
                                                $statusClass = match($computer->status) {
                                                    'Working' => 'text-bg-success',
                                                    'For Maintenance' => 'text-bg-warning',
                                                    'For Checking' => 'text-bg-info',
                                                    'Retired' => 'text-bg-secondary',
                                                    default => 'text-bg-secondary',
                                                };
                                            @endphp

                                            <span class="badge {{ $statusClass }}">
                                                {{ $computer->status }}
                                            </span>
                                        </td>

                                        <td>
                                            <a href="{{ route('equipment.edit', ['type' => 'computer', 'id' => $computer->id]) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>

                                            <form action="{{ route('equipment.destroy', ['type' => 'computer', 'id' => $computer->id]) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete this computer?')">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-outline-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            No computers match your search or filters.
                                        </td>
                                    </tr>

                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>

            @endif

            <!-- Printers -->
            @if(($filters['type'] ?? 'all') !== 'computer')

                <div class="content-card">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Printer Inventory</h5>

                        <span class="badge text-bg-primary">
                            {{ $printers->count() }} Records
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">

                            <thead>
                                <tr>
                                    <th>Printer Name</th>
                                    <th>Brand / Model</th>
                                    <th>Serial Number</th>
                                    <th>Office</th>
                                    <th>Assigned Employee</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($printers as $printer)

                                    <tr>
                                        <td>{{ $printer->printer_name }}</td>

                                        <td>{{ $printer->brand_model ?? '-' }}</td>

                                        <td>{{ $printer->serial_number ?? '-' }}</td>

                                        <td>{{ $printer->office }}</td>

                                        <td>
                                            {{ $printer->employee?->full_name ?? 'Unassigned' }}
                                        </td>

                                        <td>
                                            @php
                                                $statusClass = match($printer->status) {
                                                    'Working' => 'text-bg-success',
                                                    'For Maintenance' => 'text-bg-warning',
                                                    'For Checking' => 'text-bg-info',
                                                    'Retired' => 'text-bg-secondary',
                                                    default => 'text-bg-secondary',
                                                };
                                            @endphp

                                            <span class="badge {{ $statusClass }}">
                                                {{ $printer->status }}
                                            </span>
                                        </td>

                                        <td>
                                            <a href="{{ route('equipment.edit', ['type' => 'printer', 'id' => $printer->id]) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>

                                            <form action="{{ route('equipment.destroy', ['type' => 'printer', 'id' => $printer->id]) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete this printer?')">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-outline-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No printers match your search or filters.
                                        </td>
                                    </tr>

                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>

            @endif

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
