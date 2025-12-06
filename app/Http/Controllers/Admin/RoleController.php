<?php
// [file name]: RoleController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->latest()->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = config('permissions.available_permissions');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles|max:255',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'is_default' => 'boolean'
        ]);

        // If setting as default, remove default from other roles
        if ($request->is_default) {
            Role::where('is_default', true)->update(['is_default' => false]);
        }

        Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'permissions' => $request->permissions,
            'is_default' => $request->is_default ?? false
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = config('permissions.available_permissions');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'is_default' => 'boolean'
        ]);

        // If setting as default, remove default from other roles
        if ($request->is_default) {
            Role::where('is_default', true)->where('id', '!=', $role->id)->update(['is_default' => false]);
        }

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'permissions' => $request->permissions,
            'is_default' => $request->is_default ?? $role->is_default
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that has users assigned.');
        }

        if ($role->is_default) {
            return back()->with('error', 'Cannot delete default role.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}