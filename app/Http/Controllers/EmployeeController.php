<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeController extends Controller
{
    /**
     * Display employees with advanced search and filters.
     */


/**
 * Export filtered employee records to CSV.
 */
public function exportCsv(Request $request)
{
    $employees = $this->getFilteredEmployees($request);

    $filename = 'employee-records-' . now()->format('Y-m-d-His') . '.csv';

    return response()->streamDownload(function () use ($employees) {
        $handle = fopen('php://output', 'w');

        // UTF-8 BOM for Microsoft Excel compatibility.
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'Employee ID',
            'Full Name',
            'Position',
            'Job Description',
            'Office',
            'Account Role',
            'Computers Assigned',
            'Printers Assigned',
            'Total Equipment',
            'Status',
        ]);

        $safe = function ($value) {
            $value = (string) ($value ?? '');

            // Prevent spreadsheet formula injection.
            if (preg_match('/^[=+\-@]/', $value)) {
                return "'" . $value;
            }

            return $value;
        };

        foreach ($employees as $employee) {
            fputcsv($handle, [
                $safe($employee->employee_id),
                $safe($employee->full_name),
                $safe($employee->position),
                $safe($employee->job_description),
                $safe($employee->office),
                $safe($employee->account
                    ? ucfirst(str_replace('_', ' ', $employee->account->role))
                    : 'No Account'),
                $employee->computers_count,
                $employee->printers_count,
                $employee->computers_count + $employee->printers_count,
                $safe($employee->status),
            ]);
        }

        fclose($handle);
    }, $filename, [
        'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
}

/**
 * Export filtered employee records to PDF.
 */
public function exportPdf(Request $request)
{
    $employees = $this->getFilteredEmployees($request);

    $pdf = Pdf::loadView('employees.print', [
        'employees' => $employees,
        'filters' => $this->getEmployeeFilters($request),
    ])->setPaper('a4', 'landscape');

    return $pdf->download(
        'employee-records-' . now()->format('Y-m-d-His') . '.pdf'
    );
}

/**
 * Display employee records in a printable page.
 */
public function print(Request $request)
{
    $employees = $this->getFilteredEmployees($request);

    $filters = $this->getEmployeeFilters($request);

    return view('employees.print', compact(
        'employees',
        'filters'
    ));
}

/**
 * Retrieve employees using the existing search and filters.
 */
private function getFilteredEmployees(Request $request)
{
    $filters = $this->getEmployeeFilters($request);

    $query = Employee::with('account')
        ->withCount(['computers', 'printers']);

    $search = $filters['search'];
    $status = $filters['status'];
    $office = $filters['office'];
    $equipment = $filters['equipment'];
    $sort = $filters['sort'];

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('full_name', 'ILIKE', "%{$search}%")
                ->orWhere('employee_id', 'ILIKE', "%{$search}%")
                ->orWhere('position', 'ILIKE', "%{$search}%")
                ->orWhere('office', 'ILIKE', "%{$search}%");
        });
    }

    if (in_array($status, ['Active', 'Inactive'], true)) {
        $query->where('status', $status);
    }

    if ($office !== '') {
        $query->where('office', $office);
    }

    if ($equipment === 'with_equipment') {
        $query->where(function ($q) {
            $q->whereHas('computers')
                ->orWhereHas('printers');
        });
    } elseif ($equipment === 'without_equipment') {
        $query->whereDoesntHave('computers')
            ->whereDoesntHave('printers');
    } elseif ($equipment === 'with_computers') {
        $query->whereHas('computers');
    } elseif ($equipment === 'with_printers') {
        $query->whereHas('printers');
    }

    switch ($sort) {
        case 'oldest':
            $query->oldest();
            break;

        case 'name_asc':
            $query->orderBy('full_name', 'asc');
            break;

        case 'name_desc':
            $query->orderBy('full_name', 'desc');
            break;

        default:
            $query->latest();
            break;
    }

    return $query->get();
}

/**
 * Normalize employee filter parameters.
 */
private function getEmployeeFilters(Request $request): array
{
    $search = $request->input('search', '');
    $status = $request->input('status', '');
    $office = $request->input('office', '');
    $equipment = $request->input('equipment', '');
    $sort = $request->input('sort', 'newest');

    return [
        'search' => is_string($search) ? trim($search) : '',
        'status' => in_array($status, ['Active', 'Inactive'], true)
            ? $status : '',
        'office' => is_string($office) ? $office : '',
        'equipment' => in_array($equipment, [
            'with_equipment',
            'without_equipment',
            'with_computers',
            'with_printers',
        ], true) ? $equipment : '',
        'sort' => in_array($sort, [
            'newest',
            'oldest',
            'name_asc',
            'name_desc',
        ], true) ? $sort : 'newest',
    ];
}

    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));

        $status = $request->input('status', '');
        $office = $request->input('office', '');
        $equipment = $request->input('equipment', '');
        $sort = $request->input('sort', 'newest');

        $query = Employee::with('account')
            ->withCount(['computers', 'printers']);

        // Search employee name, employee ID, position, and office.
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ILIKE', "%{$search}%")
                    ->orWhere('employee_id', 'ILIKE', "%{$search}%")
                    ->orWhere('position', 'ILIKE', "%{$search}%")
                    ->orWhere('office', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by employee status.
        if (in_array($status, ['Active', 'Inactive'], true)) {
            $query->where('status', $status);
        }

        // Filter by office.
        if ($office !== '') {
            $query->where('office', $office);
        }

        // Filter by assigned equipment.
        if ($equipment === 'with_equipment') {
            $query->where(function ($q) {
                $q->whereHas('computers')
                    ->orWhereHas('printers');
            });
        } elseif ($equipment === 'without_equipment') {
            $query->whereDoesntHave('computers')
                ->whereDoesntHave('printers');
        } elseif ($equipment === 'with_computers') {
            $query->whereHas('computers');
        } elseif ($equipment === 'with_printers') {
            $query->whereHas('printers');
        }

        // Sorting.
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;

            case 'name_asc':
                $query->orderBy('full_name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('full_name', 'desc');
                break;

            case 'newest':
            default:
                $query->latest();
                break;
        }

        $employees = $query->get();

        // Get available offices for the filter dropdown.
        $offices = Employee::query()
            ->whereNotNull('office')
            ->where('office', '<>', '')
            ->distinct()
            ->orderBy('office')
            ->pluck('office');

        $filters = [
            'search' => $search,
            'status' => $status,
            'office' => $office,
            'equipment' => $equipment,
            'sort' => $sort,
        ];

        return view('employees.index', compact(
            'employees',
            'offices',
            'filters'
        ));
    }

    /**
     * Display the Add Employee form.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a new employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'string',
                'max:255',
                'unique:employees,employee_id',
            ],
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],
            'position' => [
                'nullable',
                'string',
                'max:255',
            ],
            'job_description' => [
                'nullable',
                'string',
            ],
            'office' => [
                'required',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                Rule::in(['Active', 'Inactive']),
            ],
        ]);

        $employee = Employee::create($validated);

        ActivityLogger::log(
            'Created',
            'Employees',
            "Created employee: {$employee->full_name} (Employee ID: {$employee->employee_id}).",
            $employee->id
        );

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee added successfully.');
    }

    /**
     * Display the Edit Employee form.
     */
    public function edit(string $id)
    {
        $employee = Employee::findOrFail($id);

        return view('employees.edit', compact('employee'));
    }

    /**
     * Update an employee.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('employees', 'employee_id')->ignore($employee->id),
            ],
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],
            'position' => [
                'nullable',
                'string',
                'max:255',
            ],
            'job_description' => [
                'nullable',
                'string',
            ],
            'office' => [
                'required',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                Rule::in(['Active', 'Inactive']),
            ],
        ]);

      $oldStatus = $employee->status;

$employee->update($validated);

$newStatus = $employee->status;

        ActivityLogger::log(
            'Updated',
            'Employees',
            "Updated employee: {$employee->full_name} (Employee ID: {$employee->employee_id}).",
            $employee->id
        );
        // Record employee status history only when the status changes.
if ($oldStatus !== $newStatus) {
    ActivityLogger::log(
        'Status Changed',
        'Employees',
        "Employee: {$employee->full_name} "
            . "(Employee ID: {$employee->employee_id}) "
            . "Status changed from {$oldStatus} to {$newStatus}.",
        $employee->id
    );
}

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }


/**
 * Update an employee's status.
 */
public function updateStatus(Request $request, string $id)
{
    $employee = Employee::findOrFail($id);

    $validated = $request->validate([
        'status' => [
            'required',
            Rule::in(['Active', 'Inactive']),
        ],
    ]);

    $oldStatus = $employee->status;
    $newStatus = $validated['status'];

    // Prevent unnecessary updates and duplicate activity logs.
    if ($oldStatus === $newStatus) {
        return redirect()
            ->back()
            ->with('info', "Employee is already {$newStatus}.");
    }

    $employee->update([
        'status' => $newStatus,
    ]);

    ActivityLogger::log(
        'Status Changed',
        'Employees',
        "Changed employee status: {$employee->full_name} "
            . "(Employee ID: {$employee->employee_id}) "
            . "from {$oldStatus} to {$newStatus}.",
        $employee->id
    );

    return redirect()
        ->back()
        ->with(
            'success',
            "Employee status changed from {$oldStatus} to {$newStatus} successfully."
        );
}

    /**
     * Display employee details and assigned equipment.
     */
    public function show(string $id)
    {
        $employee = Employee::with([
            'account',
            'computers',
            'printers',
            'equipmentAssignments',
        ])->findOrFail($id);

        return view('employees.show', compact('employee'));
    }
}
