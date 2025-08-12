<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        $this->createRoles();
        
        // Create permissions
        $this->createPermissions();
        
        // Assign permissions to roles
        $this->assignPermissionsToRoles();
        
        // Create admin user
        $this->createAdminUser();
    }
    
    /**
     * Create default roles.
     */
    private function createRoles(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'display_name' => 'مدير النظام',
                'description' => 'يمتلك كافة الصلاحيات في النظام'
            ],
            [
                'name' => 'teacher',
                'guard_name' => 'web',
                'display_name' => 'معلم',
                'description' => 'يمتلك صلاحيات إدارة الطلاب والمجموعات والتقييمات'
            ],
            [
                'name' => 'student',
                'guard_name' => 'web',
                'display_name' => 'طالب',
                'description' => 'يمتلك صلاحيات محدودة للوصول إلى المحتوى التعليمي والتقييمات'
            ],
            [
                'name' => 'branch-manager',
                'guard_name' => 'web',
                'display_name' => 'مدير فرع',
                'description' => 'يمتلك صلاحيات إدارة فرع محدد والمعلمين والطلاب التابعين له'
            ],
            [
                'name' => 'supervisor',
                'guard_name' => 'web',
                'display_name' => 'مشرف',
                'description' => 'يمتلك صلاحيات الإشراف على المعلمين والطلاب ومتابعة التقدم'
            ]
        ];
        
        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
    
    /**
     * Create permissions.
     */
    private function createPermissions(): void
    {
        $permissionGroups = [
            'إدارة النظام' => [
                ['name' => 'manage-system', 'display_name' => 'إدارة النظام', 'description' => 'إدارة إعدادات النظام العامة'],
                ['name' => 'view-logs', 'display_name' => 'عرض السجلات', 'description' => 'عرض سجلات النظام وتتبع الأخطاء'],
                ['name' => 'manage-backups', 'display_name' => 'إدارة النسخ الاحتياطية', 'description' => 'إنشاء واستعادة النسخ الاحتياطية للنظام'],
            ],
            'إدارة المستخدمين' => [
                ['name' => 'view-users', 'display_name' => 'عرض المستخدمين', 'description' => 'عرض قائمة المستخدمين'],
                ['name' => 'create-users', 'display_name' => 'إضافة مستخدمين', 'description' => 'إضافة مستخدمين جدد'],
                ['name' => 'edit-users', 'display_name' => 'تعديل المستخدمين', 'description' => 'تعديل بيانات المستخدمين'],
                ['name' => 'delete-users', 'display_name' => 'حذف المستخدمين', 'description' => 'حذف المستخدمين من النظام'],
                ['name' => 'manage-user-permissions', 'display_name' => 'إدارة صلاحيات المستخدمين', 'description' => 'تعديل أدوار وصلاحيات المستخدمين'],
            ],
            'إدارة الأدوار والصلاحيات' => [
                ['name' => 'view-roles', 'display_name' => 'عرض الأدوار', 'description' => 'عرض قائمة الأدوار'],
                ['name' => 'create-roles', 'display_name' => 'إضافة أدوار', 'description' => 'إضافة أدوار جديدة'],
                ['name' => 'edit-roles', 'display_name' => 'تعديل الأدوار', 'description' => 'تعديل الأدوار الموجودة'],
                ['name' => 'delete-roles', 'display_name' => 'حذف الأدوار', 'description' => 'حذف الأدوار من النظام'],
                ['name' => 'view-permissions', 'display_name' => 'عرض الصلاحيات', 'description' => 'عرض قائمة الصلاحيات'],
                ['name' => 'create-permissions', 'display_name' => 'إضافة صلاحيات', 'description' => 'إضافة صلاحيات جديدة'],
                ['name' => 'edit-permissions', 'display_name' => 'تعديل الصلاحيات', 'description' => 'تعديل الصلاحيات الموجودة'],
                ['name' => 'delete-permissions', 'display_name' => 'حذف الصلاحيات', 'description' => 'حذف الصلاحيات من النظام'],
            ],
            'إدارة الفروع' => [
                ['name' => 'view-branches', 'display_name' => 'عرض الفروع', 'description' => 'عرض قائمة الفروع'],
                ['name' => 'create-branches', 'display_name' => 'إضافة فروع', 'description' => 'إضافة فروع جديدة'],
                ['name' => 'edit-branches', 'display_name' => 'تعديل الفروع', 'description' => 'تعديل بيانات الفروع'],
                ['name' => 'delete-branches', 'display_name' => 'حذف الفروع', 'description' => 'حذف الفروع من النظام'],
            ],
            'إدارة المعلمين' => [
                ['name' => 'view-teachers', 'display_name' => 'عرض المعلمين', 'description' => 'عرض قائمة المعلمين'],
                ['name' => 'create-teachers', 'display_name' => 'إضافة معلمين', 'description' => 'إضافة معلمين جدد'],
                ['name' => 'edit-teachers', 'display_name' => 'تعديل المعلمين', 'description' => 'تعديل بيانات المعلمين'],
                ['name' => 'delete-teachers', 'display_name' => 'حذف المعلمين', 'description' => 'حذف المعلمين من النظام'],
            ],
            'إدارة الطلاب' => [
                ['name' => 'view-students', 'display_name' => 'عرض الطلاب', 'description' => 'عرض قائمة الطلاب'],
                ['name' => 'create-students', 'display_name' => 'إضافة طلاب', 'description' => 'إضافة طلاب جدد'],
                ['name' => 'edit-students', 'display_name' => 'تعديل الطلاب', 'description' => 'تعديل بيانات الطلاب'],
                ['name' => 'delete-students', 'display_name' => 'حذف الطلاب', 'description' => 'حذف الطلاب من النظام'],
            ],
            'إدارة المجموعات' => [
                ['name' => 'view-groups', 'display_name' => 'عرض المجموعات', 'description' => 'عرض قائمة المجموعات'],
                ['name' => 'create-groups', 'display_name' => 'إضافة مجموعات', 'description' => 'إضافة مجموعات جديدة'],
                ['name' => 'edit-groups', 'display_name' => 'تعديل المجموعات', 'description' => 'تعديل بيانات المجموعات'],
                ['name' => 'delete-groups', 'display_name' => 'حذف المجموعات', 'description' => 'حذف المجموعات من النظام'],
            ],
            'إدارة المواد' => [
                ['name' => 'view-subjects', 'display_name' => 'عرض المواد', 'description' => 'عرض قائمة المواد'],
                ['name' => 'create-subjects', 'display_name' => 'إضافة مواد', 'description' => 'إضافة مواد جديدة'],
                ['name' => 'edit-subjects', 'display_name' => 'تعديل المواد', 'description' => 'تعديل بيانات المواد'],
                ['name' => 'delete-subjects', 'display_name' => 'حذف المواد', 'description' => 'حذف المواد من النظام'],
            ],
            'إدارة التسجيل' => [
                ['name' => 'view-enrollments', 'display_name' => 'عرض التسجيلات', 'description' => 'عرض قائمة تسجيلات الطلاب'],
                ['name' => 'create-enrollments', 'display_name' => 'إضافة تسجيلات', 'description' => 'تسجيل الطلاب في المجموعات'],
                ['name' => 'edit-enrollments', 'display_name' => 'تعديل التسجيلات', 'description' => 'تعديل تسجيلات الطلاب'],
                ['name' => 'delete-enrollments', 'display_name' => 'حذف التسجيلات', 'description' => 'إلغاء تسجيل الطلاب من المجموعات'],
            ],
            'إدارة الحضور' => [
                ['name' => 'view-attendance', 'display_name' => 'عرض سجلات الحضور', 'description' => 'عرض سجلات حضور الطلاب'],
                ['name' => 'create-attendance', 'display_name' => 'تسجيل الحضور', 'description' => 'تسجيل حضور الطلاب'],
                ['name' => 'edit-attendance', 'display_name' => 'تعديل سجلات الحضور', 'description' => 'تعديل سجلات حضور الطلاب'],
                ['name' => 'delete-attendance', 'display_name' => 'حذف سجلات الحضور', 'description' => 'حذف سجلات حضور الطلاب'],
            ],
            'إدارة التقييمات' => [
                ['name' => 'view-grades', 'display_name' => 'عرض التقييمات', 'description' => 'عرض تقييمات الطلاب'],
                ['name' => 'create-grades', 'display_name' => 'إضافة تقييمات', 'description' => 'إضافة تقييمات جديدة للطلاب'],
                ['name' => 'edit-grades', 'display_name' => 'تعديل التقييمات', 'description' => 'تعديل تقييمات الطلاب'],
                ['name' => 'delete-grades', 'display_name' => 'حذف التقييمات', 'description' => 'حذف تقييمات الطلاب'],
            ],
            'إدارة التقارير' => [
                ['name' => 'view-reports', 'display_name' => 'عرض التقارير', 'description' => 'عرض تقارير النظام'],
                ['name' => 'export-reports', 'display_name' => 'تصدير التقارير', 'description' => 'تصدير التقارير بتنسيقات مختلفة'],
                ['name' => 'print-reports', 'display_name' => 'طباعة التقارير', 'description' => 'طباعة تقارير النظام'],
            ],
        ];
        
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::updateOrCreate(
                    ['name' => $permission['name']],
                    [
                        'name' => $permission['name'],
                        'guard_name' => 'web',
                        'display_name' => $permission['display_name'],
                        'description' => $permission['description'],
                        'group' => $group,
                    ]
                );
            }
        }
    }
    
    /**
     * Assign permissions to roles.
     */
    private function assignPermissionsToRoles(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $teacherRole = Role::where('name', 'teacher')->first();
        $studentRole = Role::where('name', 'student')->first();
        $branchManagerRole = Role::where('name', 'branch-manager')->first();
        $supervisorRole = Role::where('name', 'supervisor')->first();
        
        // Get all permissions
        $allPermissions = Permission::all();
        
        // Assign all permissions to admin role
        DB::table('role_has_permissions')->where('role_id', $adminRole->id)->delete();
        foreach ($allPermissions as $permission) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $permission->id,
                'role_id' => $adminRole->id
            ]);
        }
        
        // Teacher permissions
        $teacherPermissions = [
            'view-students', 'view-groups', 'view-subjects',
            'view-enrollments', 'view-attendance', 'create-attendance', 'edit-attendance',
            'view-grades', 'create-grades', 'edit-grades',
            'view-reports', 'print-reports'
        ];
        
        DB::table('role_has_permissions')->where('role_id', $teacherRole->id)->delete();
        foreach ($teacherPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permission->id,
                    'role_id' => $teacherRole->id
                ]);
            }
        }
        
        // Student permissions
        $studentPermissions = [
            'view-grades', 'view-attendance'
        ];
        
        DB::table('role_has_permissions')->where('role_id', $studentRole->id)->delete();
        foreach ($studentPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permission->id,
                    'role_id' => $studentRole->id
                ]);
            }
        }
        
        // Branch manager permissions
        $branchManagerPermissions = [
            'view-users', 'create-users', 'edit-users',
            'view-teachers', 'create-teachers', 'edit-teachers',
            'view-students', 'create-students', 'edit-students', 'delete-students',
            'view-groups', 'create-groups', 'edit-groups', 'delete-groups',
            'view-subjects', 'create-subjects', 'edit-subjects',
            'view-enrollments', 'create-enrollments', 'edit-enrollments', 'delete-enrollments',
            'view-attendance', 'create-attendance', 'edit-attendance',
            'view-grades', 'create-grades', 'edit-grades',
            'view-reports', 'export-reports', 'print-reports'
        ];
        
        DB::table('role_has_permissions')->where('role_id', $branchManagerRole->id)->delete();
        foreach ($branchManagerPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permission->id,
                    'role_id' => $branchManagerRole->id
                ]);
            }
        }
        
        // Supervisor permissions
        $supervisorPermissions = [
            'view-teachers', 'view-students',
            'view-groups', 'view-subjects',
            'view-enrollments', 'create-enrollments', 'edit-enrollments',
            'view-attendance', 'create-attendance', 'edit-attendance',
            'view-grades', 'create-grades', 'edit-grades',
            'view-reports', 'export-reports', 'print-reports'
        ];
        
        DB::table('role_has_permissions')->where('role_id', $supervisorRole->id)->delete();
        foreach ($supervisorPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permission->id,
                    'role_id' => $supervisorRole->id
                ]);
            }
        }
    }
    
    /**
     * Create admin user.
     */
    private function createAdminUser(): void
    {
        // Create admin user if not exists
        $admin = User::updateOrCreate(
            ['email' => 'admin@quransystem.com'],
            [
                'name' => 'مدير النظام',
                'email' => 'admin@quransystem.com',
                'password' => Hash::make('Admin@123'),
                'email_verified_at' => now(),
            ]
        );
        
        // Assign admin role to admin user
        $adminRole = Role::where('name', 'admin')->first();
        
        // Clear existing roles
        DB::table('model_has_roles')
            ->where('model_id', $admin->id)
            ->where('model_type', User::class)
            ->delete();
        
        // Assign admin role
        DB::table('model_has_roles')->insert([
            'role_id' => $adminRole->id,
            'model_id' => $admin->id,
            'model_type' => User::class
        ]);
    }
}
