<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('account.employee')
            ->latest();

        // Search activity logs
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('account', function ($accountQuery) use ($search) {
                        $accountQuery->where('username', 'like', "%{$search}%")
                            ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                                $employeeQuery->where('full_name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        $logs = $query->paginate(15)->withQueryString();

        $modules = ActivityLog::select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('activity_logs.index', compact('logs', 'modules'));
    }
}
