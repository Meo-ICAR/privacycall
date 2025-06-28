<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class RolePermissionController extends Controller
{
    // Show all users in the admin's company and their roles/permissions
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            // For superadmin: show all companies and their admins
            $companies = Company::with(['users' => function($query) {
                $query->whereHas('roles', function($q) {
                    $q->where('name', 'admin');
                });
            }])->get();

            $roles = Role::all();
            $permissions = Permission::all();

            return view('roles_permissions.index', compact('companies', 'roles', 'permissions'));
        } else {
            // For regular admins: show users in their company
            $companyId = $user->company_id;
            $users = User::where('company_id', $companyId)->get();
            $roles = Role::all();
            $permissions = Permission::all();

            return view('roles_permissions.index', compact('users', 'roles', 'permissions'));
        }
    }

    // Assign roles and permissions to a user
    public function assign(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'array',
            'permissions' => 'array',
        ]);
        $user = User::findOrFail($request->user_id);
        $user->syncRoles($request->roles ?? []);
        $user->syncPermissions($request->permissions ?? []);
        return redirect()->route('roles.permissions.index')->with('success', 'Roles and permissions updated successfully.');
    }
}
