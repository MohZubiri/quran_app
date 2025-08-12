<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('permission:view-permissions')->only(['index', 'show']);
        $this->middleware('permission:create-permissions')->only(['create', 'store']);
        $this->middleware('permission:edit-permissions')->only(['edit', 'update']);
        $this->middleware('permission:delete-permissions')->only('destroy');
    }

    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        $permissionsByGroup = Permission::orderBy('group')->get()->groupBy('group');
        
        return view('admin.permissions.index', compact('permissionsByGroup'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        // Get all existing permission groups for dropdown
        $groups = Permission::distinct('group')->pluck('group');
        
        return view('admin.permissions.create', compact('groups'));
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'group' => ['required', 'string', 'max:255'],
        ]);
        
        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'group' => $validated['group'],
        ]);
        
        return redirect()->route('admin.permissions.index')
            ->with('success', 'تم إنشاء الصلاحية بنجاح');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        // Get all existing permission groups for dropdown
        $groups = Permission::distinct('group')->pluck('group');
        
        return view('admin.permissions.edit', compact('permission', 'groups'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'group' => ['required', 'string', 'max:255'],
        ]);
        
        $permission->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'group' => $validated['group'],
        ]);
        
        return redirect()->route('admin.permissions.index')
            ->with('success', 'تم تحديث الصلاحية بنجاح');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        // Check if this permission is used by any roles
        $usedByRoles = $permission->roles()->exists();
        
        if ($usedByRoles) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'لا يمكن حذف هذه الصلاحية لأنها مستخدمة من قبل أدوار في النظام');
        }
        
        $permission->delete();
        
        return redirect()->route('admin.permissions.index')
            ->with('success', 'تم حذف الصلاحية بنجاح');
    }
}
