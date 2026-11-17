
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Status History | Inventory System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">Employee Status History</h3>
            <p class="text-muted mb-0">
                {{ $employee->full_name }}
                ({{ $employee->employee_id }})
            </p>
        </div>

        <a href="{{ route('employees.show', $employee->id) }}"
           class="btn btn-outline-secondary">
            Back to Employee Details
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <strong>Status Change Records</strong>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Previous Status</th>
                            <th>New Status</th>
                            <th>Description</th>
                            <th>Changed By</th>
                            <th>Date and Time</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($statusHistory as $history)
                            <tr>
                                <td>
                                    @if(str_contains($history->description, 'from Active to Inactive'))
                                        <span class="badge text-bg-success">Active</span>
                                    @elseif(str_contains($history->description, 'from Inactive to Active'))
                                        <span class="badge text-bg-secondary">Inactive</span>
                                    @else
                                        <span class="text-muted">Unknown</span>
                                    @endif
                                </td>

                                <td>
                                    @if(str_contains($history->description, 'from Active to Inactive'))
                                        <span class="badge text-bg-secondary">Inactive</span>
                                    @elseif(str_contains($history->description, 'from Inactive to Active'))
                                        <span class="badge text-bg-success">Active</span>
                                    @else
                                        <span class="text-muted">Unknown</span>
                                    @endif
                                </td>

                                <td>{{ $history->description }}</td>

                                <td>
                                    {{ $history->account->username ?? 'Unknown User' }}
                                </td>

                                <td>
                                    {{ $history->created_at->format('M d, Y h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No status changes have been recorded for this employee.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                <small class="text-muted">
                    Showing {{ $statusHistory->firstItem() ?? 0 }}
                    to {{ $statusHistory->lastItem() ?? 0 }}
                    of {{ $statusHistory->total() }} records
                </small>

                {{ $statusHistory->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

</body>
</html>
