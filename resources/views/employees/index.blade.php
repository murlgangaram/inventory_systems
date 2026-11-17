
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management | Inventory System</title>

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
            margin-bottom: 6px;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <aside class="col-md-3 col-lg-2 sidebar">
            <h5 class="mb-4">Inventory System</h5>

            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('equipment.index') }}">Equipment Record</a>
            <a href="{{ route('employees.index') }}" class="active">Employee Management</a>
            <a href="#">Account Management</a>
            <a href="#">Role Management</a>
            <a href="#">Activity Logs</a>
        </aside>

        <main class="col-md-9 col-lg-10 main-content">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h3>Employee Management</h3>
                    <p class="text-muted mb-0">
                        Manage employee information and assigned equipment.
                    </p>
                </div>

              <div class="d-flex flex-wrap gap-2">

    <a href="{{ route('employees.export.csv', request()->query()) }}"
       class="btn btn-success">
        Export CSV
    </a>

    <a href="{{ route('employees.export.pdf', request()->query()) }}"
       class="btn btn-danger">
        Export PDF
    </a>

    <a href="{{ route('employees.print', request()->query()) }}"
       class="btn btn-outline-dark"
       target="_blank">
        Print
    </a>

    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        + Add Employee
    </a>

</div>
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

            <!-- Advanced Search and Filters -->
            <div class="content-card mb-4">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="mb-1">Advanced Search & Filters</h5>
                        <small class="text-muted">
                            Search and filter employee records.
                        </small>
                    </div>
                </div>

                <form method="GET" action="{{ route('employees.index') }}">

                    <!-- Search -->
                    <div class="mb-3">
                        <label class="filter-label">Search Employee</label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by employee name, ID, position, or office..."
                            value="{{ $filters['search'] ?? '' }}"
                        >
                    </div>

                    <div class="row g-3">

                        <!-- Status Filter -->
                        <div class="col-md-6 col-lg-3">
                            <label class="filter-label">Employee Status</label>

                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>

                                <option value="Active"
                                    @selected(($filters['status'] ?? '') === 'Active')>
                                    Active
                                </option>

                                <option value="Inactive"
                                    @selected(($filters['status'] ?? '') === 'Inactive')>
                                    Inactive
                                </option>
                            </select>
                        </div>

                        <!-- Office Filter -->
                        <div class="col-md-6 col-lg-3">
                            <label class="filter-label">Office / Department</label>

                            <select name="office" class="form-select">
                                <option value="">All Offices</option>

                                @foreach($offices as $office)
                                    <option value="{{ $office }}"
                                        @selected(($filters['office'] ?? '') === $office)>
                                        {{ $office }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Equipment Filter -->
                        <div class="col-md-6 col-lg-3">
                            <label class="filter-label">Assigned Equipment</label>

                            <select name="equipment" class="form-select">
                                <option value="">All Employees</option>

                                <option value="with_equipment"
                                    @selected(($filters['equipment'] ?? '') === 'with_equipment')>
                                    With Equipment
                                </option>

                                <option value="without_equipment"
                                    @selected(($filters['equipment'] ?? '') === 'without_equipment')>
                                    Without Equipment
                                </option>

                                <option value="with_computers"
                                    @selected(($filters['equipment'] ?? '') === 'with_computers')>
                                    With Computers
                                </option>

                                <option value="with_printers"
                                    @selected(($filters['equipment'] ?? '') === 'with_printers')>
                                    With Printers
                                </option>
                            </select>
                        </div>

                        <!-- Sorting -->
                        <div class="col-md-6 col-lg-3">
                            <label class="filter-label">Sort By</label>

                            <select name="sort" class="form-select">
                                <option value="newest"
                                    @selected(($filters['sort'] ?? 'newest') === 'newest')>
                                    Newest First
                                </option>

                                <option value="oldest"
                                    @selected(($filters['sort'] ?? '') === 'oldest')>
                                    Oldest First
                                </option>

                                <option value="name_asc"
                                    @selected(($filters['sort'] ?? '') === 'name_asc')>
                                    Name A-Z
                                </option>

                                <option value="name_desc"
                                    @selected(($filters['sort'] ?? '') === 'name_desc')>
                                    Name Z-A
                                </option>
                            </select>
                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="d-flex flex-wrap gap-2 mt-4">

                        <button type="submit" class="btn btn-primary">
                            Search & Apply Filters
                        </button>

                        <a href="{{ route('employees.index') }}"
                           class="btn btn-outline-secondary">
                            Clear Filters
                        </a>

                    </div>

                </form>
            </div>

            <!-- Employee Records -->
            <div class="content-card">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="mb-1">Employee Records</h5>
                        <small class="text-muted">
                            Showing {{ $employees->count() }} employee(s).
                        </small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">

                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Full Name</th>
                                <th>Position</th>
                                <th>Office</th>
                                <th>Account</th>
                                <th>Computers</th>
                                <th>Printers</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td>{{ $employee->employee_id }}</td>

                                    <td>{{ $employee->full_name }}</td>

                                    <td>{{ $employee->position ?? '-' }}</td>

                                    <td>{{ $employee->office }}</td>

                                    <td>
                                        @if($employee->account)
                                            <span class="badge text-bg-success">
                                                {{ ucfirst(str_replace('_', ' ', $employee->account->role)) }}
                                            </span>
                                        @else
                                            <span class="badge text-bg-secondary">
                                                No Account
                                            </span>
                                        @endif
                                    </td>

                                    <td>{{ $employee->computers_count }}</td>

                                    <td>{{ $employee->printers_count }}</td>

                                    <td>
                                        @if($employee->status === 'Active')
                                            <span class="badge text-bg-success">Active</span>
                                        @else
                                            <span class="badge text-bg-secondary">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="text-nowrap">

                                        <a href="{{ route('employees.show', $employee->id) }}"
                                           class="btn btn-sm btn-outline-info">
                                            View
                                        </a>

                                        <a href="{{ route('employees.edit', $employee->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>

                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No employees found matching your search or filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
                                    </table>
                </div>

                {{-- Employee Pagination --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">

                    <div class="text-muted small">
                        Showing {{ $employees->firstItem() ?? 0 }}
                        to {{ $employees->lastItem() ?? 0 }}
                        of {{ $employees->total() }} employees
                    </div>

                    <div>
                        {{ $employees->links('pagination::bootstrap-5') }}
                    </div>

                </div>

            </div>

            </div>

        </main>
    </div>
</div>

</body>
</html>
