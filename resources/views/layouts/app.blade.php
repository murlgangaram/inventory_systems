
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Inventory System')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f3f6fb;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #172033;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding: 25px 15px;
        }

        .sidebar h4 {
            font-weight: bold;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 25px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <aside class="sidebar">
        <h4>Inventory System</h4>

        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('equipment.index') }}">Equipment Record</a>
        <a href="{{ route('employees.index') }}">Employee Management</a>

        @if(auth()->user()->role === 'super_admin')
            <hr class="border-secondary">

            <p class="text-uppercase text-secondary small fw-bold px-3 mb-2">
                Administration
            </p>

            <a href="{{ route('accounts.index') }}">Account Management</a>
            <a href="{{ route('role-management.index') }}">Role Management</a>
            <a href="{{ route('activity-logs.index') }}">Activity Logs</a>
        @endif

        <hr class="border-secondary">

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-light w-100">
                Logout
            </button>
        </form>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
