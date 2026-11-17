<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\Printer;
use App\Models\Employee;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    /**
     * Display all computers and printers.
     */
    public function index()
    {
        $computers = Computer::with('employee')
            ->latest()
            ->get();

        $printers = Printer::with('employee')
            ->latest()
            ->get();

        return view('equipment.index', compact(
            'computers',
            'printers'
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

            $equipment = Computer::create(array_merge($validated, $data));

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

            $equipment = Printer::create(array_merge($validated, $data));

            $equipmentName = $equipment->printer_name;
            $module = 'Printers';
        }

        // Record equipment creation.
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
     * Update equipment.
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
                    Rule::unique('computers', 'computer_name')->ignore($equipment->id),
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
                    'max:255',
                    Rule::unique('computers', 'serial_number')->ignore($equipment->id),
                ],
                'mac_address' => ['nullable', 'string', 'max:255'],
                'employee_id' => ['nullable', 'exists:employees,id'],
                'remarks' => ['nullable', 'string'],
                'status' => [
                    'required',
                    Rule::in(['Working', 'For Maintenance', 'Retired', 'For Checking']),
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
                    'max:255',
                    Rule::unique('printers', 'serial_number')->ignore($equipment->id),
                ],
                'office' => ['required', 'string', 'max:255'],
                'employee_id' => ['nullable', 'exists:employees,id'],
                'remarks' => ['nullable', 'string'],
                'status' => [
                    'required',
                    Rule::in(['Working', 'For Maintenance', 'Retired', 'For Checking']),
                ],
            ]);

            $equipmentName = $equipment->printer_name;
            $module = 'Printers';

        } else {
            abort(404);
        }

        $equipment->update($validated);

        // Record equipment update.
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

        // Record equipment deletion.
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
