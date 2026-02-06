<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display role list and permissions matrix.
     */
    public function index()
    {
        // Define system roles (we rely on string roles on User model)
        $roles = ['admin', 'staff', 'user'];

        $permissions = Permission::all()->groupBy('group');

        // Fetch existing role mappings
        $rolePermissions = RolePermission::all()->groupBy('role');

        return view('roles.index', compact('roles', 'permissions', 'rolePermissions'));
    }

    /**
     * Show edit form for a specific role (reused in index for simplicity or modal).
     */
    public function edit($role)
    {
        // Not used if we do single page matrix
    }

    /**
     * Update permissions for a role.
     */
    public function update(Request $request, $role)
    {
        // Validate request
        $request->validate([
            'permissions' => 'array'
        ]);

        // Sync permissions
        // 1. Delete all existing for this role
        RolePermission::where('role', $role)->delete();

        // 2. Insert new
        if ($request->has('permissions')) {
            $data = [];
            foreach ($request->permissions as $permId) {
                $data[] = [
                    'role' => $role,
                    'permission_id' => $permId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            RolePermission::insert($data);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'msg' => ucfirst($role) . ' permissions updated successfully.']);
        }

        return redirect()->route('roles.index')->with('success', ucfirst($role) . ' permissions updated successfully.');
    }
}
