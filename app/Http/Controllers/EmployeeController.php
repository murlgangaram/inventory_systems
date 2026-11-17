<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display all employees.
     */
    public function index()
    {
        $employees = Employee::with('account')
            ->withCount(['computers', 'printers'])
            ->latest()
            ->get();

        return view('employees.index', compact('employees'));
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

        // Record employee creation.
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

        $employee->update($validated);

        // Record employee update.
        ActivityLogger::log(
            'Updated',
            'Employees',
            "Updated employee: {$employee->full_name} (Employee ID: {$employee->employee_id}).",
            $employee->id
        );

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Display employee details and assigned equipment.
     */
    public function show(string $id)
    {
        $employee = Employee::with(['account', 'computers', 'printers'])
            ->findOrFail($id);

        return view('employees.show', compact('employee'));
    }

    /**
     * Delete an employee.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee->account()->exists()) {
            return redirect()
                ->route('employees.index')
                ->with('error', 'This employee has an account. Remove or reassign the account before deleting the employee.');
        }

        // Store employee details before deleting the record.
        $employeeName = $employee->full_name;
        $employeeCode = $employee->employee_id;
        $employeeRecordId = $employee->id;

        $employee->delete();

        // Record employee deletion.
        ActivityLogger::log(
            'Deleted',
            'Employees',
            "Deleted employee: {$employeeName} (Employee ID: {$employeeCode}).",
            $employeeRecordId
        );

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
