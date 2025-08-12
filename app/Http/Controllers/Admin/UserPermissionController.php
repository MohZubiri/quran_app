<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserPermissionController extends Controller
{
    /**
     * Display the user permissions edit form.
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        // Get all roles
        $roles = Role::all();
        
        // Get user's current roles
        $userRoles = $user->roles->pluck('id')->toArray();
        
        // Get all permissions grouped by group
        $permissionsByGroup = Permission::orderBy('group')->get()->groupBy('group');
        
        // Get user's direct permissions
        $userPermissions = $user->permissions->pluck('id')->toArray();
        
        // Get permissions from roles
        $rolePermissions = $user->getPermissionsViaRoles();
        
        return view('admin.users.permissions', compact(
            'user', 
            'roles', 
            'userRoles', 
            'permissionsByGroup', 
            'userPermissions', 
            'rolePermissions'
        ));
    }
    
    /**
     * Display the permissions for a specific user.
     */
    public function show(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles()->pluck('id')->toArray();
        
        $permissionsByGroup = Permission::orderBy('group')->get()->groupBy('group');
        $userPermissions = $user->permissions()->pluck('id')->toArray();
        
        // Get permissions through roles
        $rolePermissions = [];
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $rolePermissions[] = $permission->id;
            }
        }
        
        return view('admin.users.permissions', compact(
            'user', 
            'roles', 
            'userRoles', 
            'permissionsByGroup', 
            'userPermissions', 
            'rolePermissions'
        ));
    }
    
    /**
     * Update the user's roles.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);
        
        // Clear existing roles
        DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', User::class)
            ->delete();
        
        // Assign new roles
        if ($request->has('roles')) {
            foreach ($request->roles as $roleId) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_id' => $user->id,
                    'model_type' => User::class
                ]);
            }
        }
        
        return redirect()->back()->with('success', 'تم تحديث أدوار المستخدم بنجاح');
    }
    
    /**
     * Update the user's direct permissions.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        // Clear existing direct permissions
        DB::table('model_has_permissions')
            ->where('model_id', $user->id)
            ->where('model_type', User::class)
            ->delete();
        
        // Assign new direct permissions
        if ($request->has('permissions')) {
            foreach ($request->permissions as $permissionId) {
                DB::table('model_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'model_id' => $user->id,
                    'model_type' => User::class
                ]);
            }
        }
        
        return redirect()->back()->with('success', 'تم تحديث صلاحيات المستخدم بنجاح');
    }
}
