<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AccountController;
use App\Models\Computer;
use App\Models\Printer;
use App\Models\Employee;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\RoleManagementController;
use App\Models\ActivityLog;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard
   
Route::get('/dashboard', function () {

    // Basic statistics
    $computerCount = Computer::count();
    $printerCount = Printer::count();
    $employeeCount = Employee::count();

    $totalEquipment = $computerCount + $printerCount;

    // Equipment status summary
    $statuses = [
        'Working',
        'For Maintenance',
        'Retired',
        'For Checking',
    ];

    $statusCounts = [];

    foreach ($statuses as $status) {
        $statusCounts[$status] =
            Computer::where('status', $status)->count()
            + Printer::where('status', $status)->count();
    }

    // Equipment distribution by office
    $officeCounts = collect();

    $computersByOffice = Computer::selectRaw('office, COUNT(*) as total')
        ->groupBy('office')
        ->get();

    $printersByOffice = Printer::selectRaw('office, COUNT(*) as total')
        ->groupBy('office')
        ->get();

    foreach ($computersByOffice as $item) {
        $officeCounts->put(
            $item->office,
            $officeCounts->get($item->office, 0) + $item->total
        );
    }

    foreach ($printersByOffice as $item) {
        $officeCounts->put(
            $item->office,
            $officeCounts->get($item->office, 0) + $item->total
        );
    }

    $officeCounts = $officeCounts->sortDesc();

    // Recent activity logs
    $recentActivities = ActivityLog::with('account.employee')
        ->latest()
        ->take(8)
        ->get();

    return view('dashboard', compact(
        'computerCount',
        'printerCount',
        'employeeCount',
        'totalEquipment',
        'statusCounts',
        'officeCounts',
        'recentActivities'
    ));

})->name('dashboard');

 


    /*
    |--------------------------------------------------------------------------
    | Equipment Management
    |--------------------------------------------------------------------------
    */

    // All authenticated users can view equipment.
    Route::get('/equipment', [EquipmentController::class, 'index'])
        ->name('equipment.index');

        // Export equipment records to CSV.
Route::get('/equipment/export/csv', [EquipmentController::class, 'exportCsv'])
    ->name('equipment.export.csv');

// Print equipment records.
Route::get('/equipment/print', [EquipmentController::class, 'print'])
    ->name('equipment.print');

    Route::get('/equipment/export/pdf', [EquipmentController::class, 'exportPdf'])
    ->name('equipment.export.pdf');

    // Super Admin and Admin can create and edit equipment.
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/equipment/create', [EquipmentController::class, 'create'])
            ->name('equipment.create');

        Route::post('/equipment', [EquipmentController::class, 'store'])
            ->name('equipment.store');

        Route::get('/equipment/{type}/{id}/edit', [EquipmentController::class, 'edit'])
            ->name('equipment.edit');

        Route::put('/equipment/{type}/{id}', [EquipmentController::class, 'update'])
            ->name('equipment.update');
    });

    // Only Super Admin can delete equipment.
    Route::delete('/equipment/{type}/{id}', [EquipmentController::class, 'destroy'])
        ->middleware('role:super_admin')
        ->name('equipment.destroy');



  /*
|--------------------------------------------------------------------------
| Employee Management
|--------------------------------------------------------------------------
*/

// All authenticated users can view the employee list.
Route::get('/employees', [EmployeeController::class, 'index'])
    ->name('employees.index');
    // Export employee records to CSV.
Route::get('/employees/export/csv', [EmployeeController::class, 'exportCsv'])
    ->name('employees.export.csv');

// Export employee records to PDF.
Route::get('/employees/export/pdf', [EmployeeController::class, 'exportPdf'])
    ->name('employees.export.pdf');

// Print employee records.
Route::get('/employees/print', [EmployeeController::class, 'print'])
    ->name('employees.print');

// Super Admin and Admin can create and edit employees.
Route::middleware('role:super_admin,admin')->group(function () {
    // Change employee status (Active / Inactive).
Route::patch('/employees/{id}/status', [EmployeeController::class, 'updateStatus'])
    ->whereNumber('id')
    ->name('employees.updateStatus');

    Route::get('/employees/create', [EmployeeController::class, 'create'])
        ->name('employees.create');

    Route::post('/employees', [EmployeeController::class, 'store'])
        ->name('employees.store');

    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])
        ->name('employees.edit');

    Route::put('/employees/{id}', [EmployeeController::class, 'update'])
        ->name('employees.update');
});

// View employee status history.
Route::get('/employees/{id}/status-history', [EmployeeController::class, 'statusHistory'])
    ->whereNumber('id')
    ->name('employees.statusHistory');
    
// All authenticated users can view employee details.
Route::get('/employees/{id}', [EmployeeController::class, 'show'])
    ->whereNumber('id')
    ->name('employees.show');

    /*
    |--------------------------------------------------------------------------
    | Account Management
    |--------------------------------------------------------------------------
    */

    // Only Super Admin can manage system accounts.
 Route::middleware('role:super_admin')->group(function () {

    // Account Management
    Route::resource('accounts', AccountController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index');

        // Role Management
Route::get('/role-management', [RoleManagementController::class, 'index'])
    ->name('role-management.index');

});

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});
