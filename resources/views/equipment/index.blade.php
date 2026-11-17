
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Records | Inventory System</title>

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
            <a href="{{ route('equipment.index') }}" class="active">Equipment Record</a>
            <a href="#">Assigned Employees</a>
            <a href="#">Account Management</a>
            <a href="#">Role Management</a>
            <a href="#">Activity Logs</a>
        </aside>

        <main class="col-md-9 col-lg-10 main-content">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h3>Equipment Records</h3>
                    <p class="text-muted mb-0">Manage computers and printers.</p>
                </div>

                <a href="{{ route('equipment.create') }}" class="btn btn-primary">
                    + Add Equipment
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
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

            <!-- Computers -->
            <div class="content-card mb-4">

                <h5 class="mb-3">Computer Inventory</h5>

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
                                    <td>{{ $computer->employee?->full_name ?? 'Unassigned' }}</td>
                                    <td>
                                        <span class="badge text-bg-secondary">
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
                                        No computers registered yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Printers -->
            <div class="content-card">

                <h5 class="mb-3">Printer Inventory</h5>

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
                                    <td>{{ $printer->employee?->full_name ?? 'Unassigned' }}</td>
                                    <td>
                                        <span class="badge text-bg-secondary">
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
                                        No printers registered yet.
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
