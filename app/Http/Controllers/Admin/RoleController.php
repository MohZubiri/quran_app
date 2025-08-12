<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-roles')->only(['index', 'show']);
        $this->middleware('permission:create-roles')->only(['create', 'store']);
        $this->middleware('permission:edit-roles')->only(['edit', 'update']);
        $this->middleware('permission:delete-roles')->only('destroy');
    }

    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::withCount('users', 'permissions')->get();
        
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissionsByGroup = Permission::orderBy('group')->get()->groupBy('group');
        
        return view('admin.roles.create', compact('permissionsByGroup'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
        
        DB::transaction(function () use ($validated) {
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => 'web',
                'display_name' => $validated['display_name'],
                'description' => $validated['description'],
            ]);
            
            $role->permissions()->sync($validated['permissions']);
        });
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'تم إنشاء الدور بنجاح');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        $permissionsByGroup = $role->permissions->groupBy('group');
        $users = User::whereHas('roles', function ($query) use ($role) {
            $query->where('id', $role->id);
        })->paginate(10);
        
        return view('admin.roles.show', compact('role', 'permissionsByGroup', 'users'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissionsByGroup = Permission::orderBy('group')->get()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissionsByGroup', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
        
        DB::transaction(function () use ($validated, $role) {
            $role->update([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'],
            ]);
            
            $role->permissions()->sync($validated['permissions']);
        });
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'تم تحديث الدور بنجاح');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Check if this role is used by any users
        $usedByUsers = $role->users()->exists();
        
        if ($usedByUsers) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن حذف هذا الدور لأنه مستخدم من قبل مستخدمين في النظام');
        }
        
        // Check if this is a system role (admin, teacher, student)
        if (in_array($role->name, ['admin', 'teacher', 'student'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن حذف الأدوار الأساسية في النظام');
        }
        
        $role->delete();
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'تم حذف الدور بنجاح');
    }
    
    /**
     * Assign users to a role.
     */
    public function assignUsers(Request $request, Role $role)
    {
        $validated = $request->validate([
            'users' => ['required', 'array'],
            'users.*' => ['exists:users,id'],
        ]);
        
        foreach ($validated['users'] as $userId) {
            // Check if the user already has this role
            $exists = DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->where('model_id', $userId)
                ->where('model_type', User::class)
                ->exists();
                
            if (!$exists) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $role->id,
                    'model_id' => $userId,
                    'model_type' => User::class
                ]);
            }
        }
        
        return redirect()->route('admin.roles.show', $role)
            ->with('success', 'تم إضافة المستخدمين للدور بنجاح');
    }
    
    /**
     * Remove a user from a role.
     */
    public function removeUser(Role $role, User $user)
    {
        // Check if this is the admin role and the user is the last admin
        if ($role->name === 'admin') {
            $adminCount = DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->count();
                
            if ($adminCount <= 1) {
                return redirect()->route('admin.roles.show', $role)
                    ->with('error', 'لا يمكن إزالة آخر مستخدم من دور المدير');
            }
        }
        
        DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->where('model_id', $user->id)
            ->where('model_type', User::class)
            ->delete();
            
        return redirect()->route('admin.roles.show', $role)
            ->with('success', 'تم إزالة المستخدم من الدور بنجاح');
    }
}
