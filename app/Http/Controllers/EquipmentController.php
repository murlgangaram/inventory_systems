<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Employee;
use App\Models\EquipmentAssignment;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class EquipmentController extends Controller
{
    /**
     * Display all computers and printers.
     */
  
/**
 * Export filtered equipment records to CSV.
 */
public function exportCsv(Request $request)
{
    $data = $this->getFilteredEquipment($request);

    $filename = 'equipment-inventory-' . now()->format('Y-m-d-His') . '.csv';

    return response()->streamDownload(function () use ($data) {
        $handle = fopen('php://output', 'w');

        // UTF-8 BOM for Excel compatibility.
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'Equipment Type',
            'Equipment Name',
            'Brand / Model',
            'RAM',
            'CPU',
            'SSD',
            'HD',
            'Serial Number',
            'MAC Address',
            'Office',
            'Assigned Employee',
            'Status',
            'Remarks',
        ]);

        $safe = function ($value) {
            $value = (string) ($value ?? '');

            // Prevent spreadsheet formula injection.
            if (preg_match('/^[=+\-@]/', $value)) {
                return "'" . $value;
            }

            return $value;
        };

        foreach ($data['computers'] as $computer) {
            fputcsv($handle, [
                'Computer',
                $safe($computer->computer_name),
                $safe(trim(($computer->brand ?? '') . ' ' . ($computer->model ?? ''))),
                $safe($computer->ram),
                $safe($computer->cpu),
                $safe($computer->ssd),
                $safe($computer->hd),
                $safe($computer->serial_number),
                $safe($computer->mac_address),
                $safe($computer->office),
                $safe($computer->employee->full_name ?? 'Unassigned'),
                $safe($computer->status),
                $safe($computer->remarks),
            ]);
        }

        foreach ($data['printers'] as $printer) {
            fputcsv($handle, [
                'Printer',
                $safe($printer->printer_name),
                $safe($printer->brand_model),
                '',
                '',
                '',
                '',
                $safe($printer->serial_number),
                '',
                $safe($printer->office),
                $safe($printer->employee->full_name ?? 'Unassigned'),
                $safe($printer->status),
                $safe($printer->remarks),
            ]);
        }

        fclose($handle);
    }, $filename, [
        'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
}

/**
 * Export filtered equipment records to PDF.
 */
public function exportPdf(Request $request)
{
    $data = $this->getFilteredEquipment($request);

    $pdf = Pdf::loadView('equipment.print', $data)
        ->setPaper('a4', 'landscape');

    return $pdf->download(
        'equipment-inventory-' . now()->format('Y-m-d-His') . '.pdf'
    );
}

/**
 * Display filtered equipment records in a printable page.
 */
public function print(Request $request)
{
    $data = $this->getFilteredEquipment($request);

    return view('equipment.print', $data);
}

/**
 * Retrieve equipment using the same filters and sorting as index().
 */
private function getFilteredEquipment(Request $request): array
{
    $search = trim((string) $request->input('search', ''));

    $type = $request->input('type', 'all');
    $status = $request->input('status', 'all');
    $office = $request->input('office', 'all');
    $employeeId = $request->input('employee_id', 'all');
    $assignment = $request->input('assignment', 'all');
    $sort = $request->input('sort', 'newest');

    if (!in_array($type, ['all', 'computer', 'printer'], true)) {
        $type = 'all';
    }

    if (!in_array($status, [
        'all', 'Working', 'For Maintenance', 'For Checking', 'Retired'
    ], true)) {
        $status = 'all';
    }

    if (!is_scalar($office)) {
        $office = 'all';
    }

    if (!in_array($assignment, ['all', 'assigned', 'unassigned'], true)) {
        $assignment = 'all';
    }

    if (!in_array($sort, [
        'newest', 'oldest', 'name_asc', 'name_desc',
        'status_asc', 'status_desc'
    ], true)) {
        $sort = 'newest';
    }

    if (
        $employeeId !== 'all' &&
        (!is_scalar($employeeId) || !ctype_digit((string) $employeeId))
    ) {
        $employeeId = 'all';
    }

    $applyFilters = function ($query, array $columns) use (
        $search,
        $status,
        $office,
        $employeeId,
        $assignment
    ) {
        if ($search !== '') {
            $query->where(function ($q) use ($search, $columns) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'ILIKE', "%{$search}%");
                }

                $q->orWhereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery->where('full_name', 'ILIKE', "%{$search}%");
                });
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($office !== 'all') {
            $query->where('office', $office);
        }

        if ($employeeId !== 'all') {
            $query->where('employee_id', (int) $employeeId);
        }

        if ($assignment === 'assigned') {
            $query->whereNotNull('employee_id');
        } elseif ($assignment === 'unassigned') {
            $query->whereNull('employee_id');
        }

        return $query;
    };

    $applySorting = function ($query, string $nameColumn) use ($sort) {
        switch ($sort) {
            case 'oldest':
                $query->oldest('created_at');
                break;

            case 'name_asc':
                $query->orderBy($nameColumn, 'asc');
                break;

            case 'name_desc':
                $query->orderBy($nameColumn, 'desc');
                break;

            case 'status_asc':
                $query->orderBy('status', 'asc');
                break;

            case 'status_desc':
                $query->orderBy('status', 'desc');
                break;

            default:
                $query->latest('created_at');
                break;
        }

        return $query;
    };

    $computers = collect();

    if ($type === 'all' || $type === 'computer') {
        $computerQuery = Computer::with('employee');

        $applyFilters($computerQuery, [
            'computer_name',
            'brand',
            'model',
            'serial_number',
            'mac_address',
            'office',
        ]);

        $computers = $applySorting(
            $computerQuery,
            'computer_name'
        )->get();
    }

    $printers = collect();

    if ($type === 'all' || $type === 'printer') {
        $printerQuery = Printer::with('employee');

        $applyFilters($printerQuery, [
            'printer_name',
            'brand_model',
            'serial_number',
            'office',
        ]);

        $printers = $applySorting(
            $printerQuery,
            'printer_name'
        )->get();
    }

    $filters = [
        'search' => $search,
        'type' => $type,
        'status' => $status,
        'office' => $office,
        'employee_id' => $employeeId,
        'assignment' => $assignment,
        'sort' => $sort,
    ];

    return compact('computers', 'printers', 'filters');
}

public function index(Request $request)
{
    // Get and validate filter values.
    $search = trim((string) $request->input('search', ''));

    $type = $request->input('type', 'all');
    $status = $request->input('status', 'all');
    $office = $request->input('office', 'all');
    $employeeId = $request->input('employee_id', 'all');
    $assignment = $request->input('assignment', 'all');
    $sort = $request->input('sort', 'newest');

    // Only allow supported filter values.
    if (!in_array($type, ['all', 'computer', 'printer'], true)) {
        $type = 'all';
    }

    if (!in_array($status, [
        'all', 'Working', 'For Maintenance', 'For Checking', 'Retired'
    ], true)) {
        $status = 'all';
    }

    if (!in_array($assignment, ['all', 'assigned', 'unassigned'], true)) {
        $assignment = 'all';
    }

    if (!in_array($sort, [
        'newest', 'oldest', 'name_asc', 'name_desc',
        'status_asc', 'status_desc'
    ], true)) {
        $sort = 'newest';
    }

    if (
        $employeeId !== 'all' &&
        (!is_scalar($employeeId) || !ctype_digit((string) $employeeId))
    ) {
        $employeeId = 'all';
    }

    // Shared filters for computers and printers.
    $applyFilters = function ($query, array $columns) use (
        $search,
        $status,
        $office,
        $employeeId,
        $assignment
    ) {
        if ($search !== '') {
            $query->where(function ($q) use ($search, $columns) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'ILIKE', "%{$search}%");
                }

                $q->orWhereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery->where('full_name', 'ILIKE', "%{$search}%");
                });
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($office !== 'all') {
            $query->where('office', $office);
        }

        if ($employeeId !== 'all') {
            $query->where('employee_id', (int) $employeeId);
        }

        if ($assignment === 'assigned') {
            $query->whereNotNull('employee_id');
        } elseif ($assignment === 'unassigned') {
            $query->whereNull('employee_id');
        }

        return $query;
    };

    // Sorting for both equipment types.
    $applySorting = function ($query, string $nameColumn) use ($sort) {
        switch ($sort) {
            case 'oldest':
                $query->oldest('created_at');
                break;

            case 'name_asc':
                $query->orderBy($nameColumn, 'asc');
                break;

            case 'name_desc':
                $query->orderBy($nameColumn, 'desc');
                break;

            case 'status_asc':
                $query->orderBy('status', 'asc');
                break;

            case 'status_desc':
                $query->orderBy('status', 'desc');
                break;

            default:
                $query->latest('created_at');
                break;
        }

        return $query;
    };

    // Computer inventory.
    $computers = collect();

    if ($type === 'all' || $type === 'computer') {
        $computerQuery = Computer::with('employee');

        $computerQuery = $applyFilters($computerQuery, [
            'computer_name',
            'brand',
            'model',
            'serial_number',
            'mac_address',
            'office',
        ]);

        $computers = $applySorting(
            $computerQuery,
            'computer_name'
        )->get();
    }

    // Printer inventory.
    $printers = collect();

    if ($type === 'all' || $type === 'printer') {
        $printerQuery = Printer::with('employee');

        $printerQuery = $applyFilters($printerQuery, [
            'printer_name',
            'brand_model',
            'serial_number',
            'office',
        ]);

        $printers = $applySorting(
            $printerQuery,
            'printer_name'
        )->get();
    }

    // Populate filter dropdowns.
    $employees = Employee::orderBy('full_name')
        ->get(['id', 'full_name']);

    $offices = Computer::whereNotNull('office')
        ->distinct()
        ->pluck('office')
        ->merge(
            Printer::whereNotNull('office')
                ->distinct()
                ->pluck('office')
        )
        ->filter()
        ->unique()
        ->sort()
        ->values();

    // Keep selected filters for the Blade view.
    $filters = [
        'search' => $search,
        'type' => $type,
        'status' => $status,
        'office' => $office,
        'employee_id' => $employeeId,
        'assignment' => $assignment,
        'sort' => $sort,
    ];

    return view('equipment.index', compact(
        'computers',
        'printers',
        'employees',
        'offices',
        'filters'
    ));
}


    /**
     * Display equipment creation form.
     */
    public function create()
    {
        $employees = Employee::where('status', 'Active')
            ->orderBy('full_name')
            ->get();

        return view('equipment.create', compact('employees'));
    }

    /**
     * Store new equipment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['computer', 'printer'])],
            'office' => ['required', 'string', 'max:255'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'remarks' => ['nullable', 'string'],
            'status' => [
                'required',
                Rule::in([
                    'Working',
                    'For Maintenance',
                    'Retired',
                    'For Checking'
                ])
            ],
        ]);

        $type = $validated['type'];
        unset($validated['type']);

        if ($type === 'computer') {
            $data = $request->validate([
                'computer_name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:computers,computer_name'
                ],
                'ram' => ['nullable', 'string', 'max:255'],
                'cpu' => ['nullable', 'string', 'max:255'],
                'ssd' => ['nullable', 'string', 'max:255'],
                'hd' => ['nullable', 'string', 'max:255'],
                'brand' => ['nullable', 'string', 'max:255'],
                'model' => ['nullable', 'string', 'max:255'],
                'serial_number' => [
                    'nullable',
                    'string',
                    'max:255',
                    'unique:computers,serial_number'
                ],
                'mac_address' => ['nullable', 'string', 'max:255'],
            ]);

            $equipment = DB::transaction(function () use ($validated, $data) {
                $equipment = Computer::create(
                    array_merge($validated, $data)
                );

                $this->recordAssignment(
                    'computer',
                    $equipment->id,
                    $equipment->employee_id
                );

                return $equipment;
            });

            $equipmentName = $equipment->computer_name;
            $module = 'Computers';

        } else {
            $data = $request->validate([
                'printer_name' => ['required', 'string', 'max:255'],
                'brand_model' => ['nullable', 'string', 'max:255'],
                'serial_number' => [
                    'nullable',
                    'string',
                    'max:255',
                    'unique:printers,serial_number'
                ],
            ]);

            $equipment = DB::transaction(function () use ($validated, $data) {
                $equipment = Printer::create(
                    array_merge($validated, $data)
                );

                $this->recordAssignment(
                    'printer',
                    $equipment->id,
                    $equipment->employee_id
                );

                return $equipment;
            });

            $equipmentName = $equipment->printer_name;
            $module = 'Printers';
        }

        ActivityLogger::log(
            'Created',
            $module,
            "Added {$module} equipment: {$equipmentName}.",
            $equipment->id
        );

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment successfully added.');
    }

    /**
     * Display equipment edit form.
     */
    public function edit(string $type, int $id)
    {
        $employees = Employee::where('status', 'Active')
            ->orderBy('full_name')
            ->get();

        if ($type === 'computer') {
            $equipment = Computer::findOrFail($id);
        } elseif ($type === 'printer') {
            $equipment = Printer::findOrFail($id);
        } else {
            abort(404);
        }

        return view('equipment.edit', compact(
            'equipment',
            'employees',
            'type'
        ));
    }

    /**
     * Update equipment and track assignment changes.
     */
    public function update(Request $request, string $type, int $id)
    {
        if ($type === 'computer') {
            $equipment = Computer::findOrFail($id);

            $validated = $request->validate([
                'computer_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('computers', 'computer_name')
                        ->ignore($equipment->id),
                ],
                'office' => ['required', 'string', 'max:255'],
                'ram' => ['nullable', 'string', 'max:255'],
                'cpu' => ['nullable', 'string', 'max:255'],
                'ssd' => ['nullable', 'string', 'max:255'],
                'hd' => ['nullable', 'string', 'max:255'],
                'brand' => ['nullable', 'string', 'max:255'],
                'model' => ['nullable', 'string', 'max:255'],
                'serial_number' => [
                    'nullable',
                    'string',
                    Rule::unique('computers', 'serial_number')
                        ->ignore($equipment->id),
                ],
                'mac_address' => ['nullable', 'string', 'max:255'],
                'employee_id' => ['nullable', 'exists:employees,id'],
                'remarks' => ['nullable', 'string'],
                'status' => [
                    'required',
                    Rule::in([
                        'Working',
                        'For Maintenance',
                        'Retired',
                        'For Checking'
                    ]),
                ],
            ]);

            $equipmentName = $equipment->computer_name;
            $module = 'Computers';

        } elseif ($type === 'printer') {
            $equipment = Printer::findOrFail($id);

            $validated = $request->validate([
                'printer_name' => ['required', 'string', 'max:255'],
                'brand_model' => ['nullable', 'string', 'max:255'],
                'serial_number' => [
                    'nullable',
                    'string',
                    Rule::unique('printers', 'serial_number')
                        ->ignore($equipment->id),
                ],
                'office' => ['required', 'string', 'max:255'],
                'employee_id' => ['nullable', 'exists:employees,id'],
                'remarks' => ['nullable', 'string'],
                'status' => [
                    'required',
                    Rule::in([
                        'Working',
                        'For Maintenance',
                        'Retired',
                        'For Checking'
                    ]),
                ],
            ]);

            $equipmentName = $equipment->printer_name;
            $module = 'Printers';

        } else {
            abort(404);
        }

        DB::transaction(function () use ($equipment, $validated, $type) {
            $previousEmployeeId = $equipment->employee_id;
            $newEmployeeId = $validated['employee_id'] ?? null;

            // Check whether the assigned employee has changed.
            if ($previousEmployeeId != $newEmployeeId) {

                // Close the previous active assignment.
                EquipmentAssignment::where('equipment_type', $type)
                    ->where('equipment_id', $equipment->id)
                    ->whereNull('returned_at')
                    ->update([
                        'returned_at' => now(),
                    ]);

                // Create a new assignment if an employee was selected.
                $this->recordAssignment(
                    $type,
                    $equipment->id,
                    $newEmployeeId
                );
            }

            $equipment->update($validated);
        });

        ActivityLogger::log(
            'Updated',
            $module,
            "Updated {$module} equipment: {$equipmentName}.",
            $equipment->id
        );

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment successfully updated.');
    }

    /**
     * Create an equipment assignment history record.
     */
    private function recordAssignment(
        string $type,
        int $equipmentId,
        ?int $employeeId
    ): void {
        if (!$employeeId) {
            return;
        }

        EquipmentAssignment::create([
            'equipment_type' => $type,
            'equipment_id' => $equipmentId,
            'employee_id' => $employeeId,
            'assigned_at' => now(),
            'returned_at' => null,
            'remarks' => null,
        ]);
    }

    /**
     * Delete equipment.
     */
    public function destroy(string $type, int $id)
    {
        if ($type === 'computer') {
            $equipment = Computer::findOrFail($id);
            $equipmentName = $equipment->computer_name;
            $module = 'Computers';

        } elseif ($type === 'printer') {
            $equipment = Printer::findOrFail($id);
            $equipmentName = $equipment->printer_name;
            $module = 'Printers';

        } else {
            abort(404);
        }

        $recordId = $equipment->id;

        $equipment->delete();

        ActivityLogger::log(
            'Deleted',
            $module,
            "Deleted {$module} equipment: {$equipmentName}.",
            $recordId
        );

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment successfully deleted.');
    }
}
