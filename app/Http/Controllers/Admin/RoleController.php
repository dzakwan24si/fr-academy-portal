<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'permissions' => ['nullable', 'array'],
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $permissions = $validated['permissions'];
            if (in_array('view_administrasi', $permissions) || in_array('edit_administrasi', $permissions)) {
                $permissions[] = 'access_administrasi';
            }
            $role->syncPermissions(array_unique($permissions));
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        if ($role->name === 'Superadmin') {
            return redirect()->route('admin.roles.index')->with('error', 'Role Superadmin tidak boleh diedit.');
        }

        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Superadmin') {
            return redirect()->route('admin.roles.index')->with('error', 'Role Superadmin tidak boleh diedit.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['nullable', 'array'],
        ]);

        $role->update(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $permissions = $validated['permissions'];
            if (in_array('view_administrasi', $permissions) || in_array('edit_administrasi', $permissions)) {
                $permissions[] = 'access_administrasi';
            }
            $role->syncPermissions(array_unique($permissions));
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Superadmin') {
            return redirect()->route('admin.roles.index')->with('error', 'Role Superadmin tidak boleh dihapus.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
