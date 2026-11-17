<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleManagementController extends Controller
{
    public function index()
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'role' => 'super_admin',
                'description' => 'Full access to all system features and management.',
                'permissions' => [
                    'View Dashboard',
                    'View Equipment',
                    'Add Equipment',
                    'Edit Equipment',
                    'Delete Equipment',
                    'View Employees',
                    'Add Employees',
                    'Edit Employees',
                    'Delete Employees',
                    'Account Management',
                    'Role Management',
                    'Activity Logs',
                ],
            ],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'description' => 'Manage equipment and employee records.',
                'permissions' => [
                    'View Dashboard',
                    'View Equipment',
                    'Add Equipment',
                    'Edit Equipment',
                    'View Employees',
                    'Add Employees',
                    'Edit Employees',
                ],
            ],
            [
                'name' => 'Staff',
                'role' => 'user',
                'description' => 'View dashboard, equipment, and employee information.',
                'permissions' => [
                    'View Dashboard',
                    'View Equipment',
                    'View Employees',
                ],
            ],
        ];

        return view('role_management.index', compact('roles'));
    }
}