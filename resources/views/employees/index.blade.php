
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
                    <p class="text-muted mb-0">Manage employee information and assigned equipment.</p>
                </div>

                <a href="{{ route('employees.create') }}" class="btn btn-primary">
                    + Add Employee
                </a>
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

            <div class="content-card">

                <h5 class="mb-3">Employee Records</h5>

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

                                    <td>
                                        <a href="{{ route('employees.show', $employee->id) }}"
                                           class="text-decoration-none fw-semibold">
                                            {{ $employee->full_name }}
                                        </a>
                                    </td>

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

                                        <form action="{{ route('employees.destroy', $employee->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Delete
                                            </button>
                                        </form>

                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No employees registered yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>

        </main>
    </div>
</div>

</body>
</html>
