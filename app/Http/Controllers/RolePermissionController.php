<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class RolePermissionController extends Controller
{
    // Show all users in the admin's company and their roles/permissions
    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $users = User::where('company_id', $companyId)->get();
        $roles = Role::all();
        $permissions = Permission::all();
        return view('roles_permissions.index', compact('users', 'roles', 'permissions'));
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
