<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAccessAdminPanelPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the permission already exists
        $permissionExists = DB::table('permissions')
            ->where('name', 'access-admin-panel')
            ->exists();
            
        if (!$permissionExists) {
            // Add the access-admin-panel permission
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => 'access-admin-panel',
                'guard_name' => 'web',
                'display_name' => 'الوصول للوحة التحكم',
                'description' => 'الوصول إلى لوحة تحكم المدير',
                'group' => 'إدارة النظام',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Get admin role ID
            $adminRole = DB::table('roles')->where('name', 'admin')->first();
            
            if ($adminRole) {
                // Assign permission to admin role
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => $adminRole->id
                ]);
            }
            
            // Get all admin users
            $adminUsers = DB::table('users')
                ->where('role', 'admin')
                ->get();
                
            // Assign permission directly to all admin users
            foreach ($adminUsers as $user) {
                DB::table('model_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'model_id' => $user->id,
                    'model_type' => 'App\\Models\\User'
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Get the permission ID
        $permission = DB::table('permissions')
            ->where('name', 'access-admin-panel')
            ->first();
            
        if ($permission) {
            // Remove role has permissions
            DB::table('role_has_permissions')
                ->where('permission_id', $permission->id)
                ->delete();
                
            // Remove model has permissions
            DB::table('model_has_permissions')
                ->where('permission_id', $permission->id)
                ->delete();
                
            // Remove the permission
            DB::table('permissions')
                ->where('id', $permission->id)
                ->delete();
        }
    }
}
