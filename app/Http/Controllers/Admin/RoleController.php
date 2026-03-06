<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions', 'users')
            ->where('guard_name', config('auth.defaults.guard'))
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', config('auth.defaults.guard'))
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0] ?? 'other');

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,guard_name,' . config('auth.defaults.guard'),
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => config('auth.defaults.guard'),
        ]);

        $role->syncPermissions($request->permissions ?? []);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully!');
    }

    public function edit(Role $role)
    {
        if ($role->guard_name !== config('auth.defaults.guard')) {
            abort(404);
        }

        $permissions = Permission::where('guard_name', config('auth.defaults.guard'))
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0] ?? 'other');

        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->guard_name !== config('auth.defaults.guard')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id . ',id,guard_name,' . config('auth.defaults.guard'),
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        if ($role->guard_name !== config('auth.defaults.guard')) {
            abort(404);
        }

        if ($role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin role cannot be deleted.');
        }

        $role->delete();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully!');
    }
}
