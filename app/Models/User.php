<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use DB;
use App\Models\Teacher;
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'branch_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
            ->where('model_type', User::class);
    }

    /**
     * The permissions that belong to the user.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'model_has_permissions', 'model_id', 'permission_id')
            ->where('model_type', User::class);
    }

    /**
     * Get the branch that owns the user.
     * For admin users, this returns all branches.
     * For other users, this returns their assigned branch.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getTeacher()
    {
      return  Teacher::where('user_id', $this->id)->first();
    }

    public function checkGroup()
    {
       
        if($this->isTeacher() )
        {
            if($this->getGroup())
            {
                return true;
            }
            return false;
        }
        
        return true;
    }
    public function getGroup()
    {
      $group=  DB::Table('groups')->join('teachers', 'groups.teacher_id', '=', 'teachers.id')
        ->where('teachers.user_id', $this->id)->select('groups.*')->first();
       
        return $group;
    }
    
    
    /**
     * Get all branches accessible to the user.
     * For admin users, this returns all branches.
     * For other users, this returns only their assigned branch.
     */
    public function branches()
    {
        if ($this->isAdmin()) {
            return Branch::all();
        }
        
        return $this->branch()->get();
    }

    /**
     * Assign the given role to the user.
     *
     * @param string|Role $role
     * @return $this
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('id', $role)->firstOrFail();
        }
        
        $this->roles()->attach($role->id, [
            'model_type' => 'App\\Models\\User'
        ]);

        return $this;
    }

    /**
     * Remove the given role from the user.
     *
     * @param string|Role $role
     * @return $this
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->detach($role);

        return $this;
    }

    /**
     * Determine if the user has the given role.
     *
     * @param string|Role $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->exists();
        }

        return $this->roles()->where('id', $role->id)->exists();
    }

    /**
     * Determine if the user has any of the given roles.
     *
     * @param array|string|Role $roles
     * @return bool
     */
    public function hasAnyRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        if ($roles instanceof Role) {
            return $this->hasRole($roles);
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the user has all of the given roles.
     *
     * @param array|string|Role $roles
     * @return bool
     */
    public function hasAllRoles($roles): bool
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        if ($roles instanceof Role) {
            return $this->hasRole($roles);
        }

        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Give the given permission to the user.
     *
     * @param string|Permission $permission
     * @return $this
     */
    public function givePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching([$permission->id]);

        return $this;
    }

    /**
     * Remove the given permission from the user.
     *
     * @param string|Permission $permission
     * @return $this
     */
    public function revokePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }

        $this->permissions()->detach($permission);

        return $this;
    }

    /**
     * Determine if the user has the given permission.
     *
     * @param string|Permission $permission
     * @return bool
     */
    public function hasPermissionTo($permission): bool
    {
        if (is_string($permission)) {
            $permissionName = $permission;
        } else {
            $permissionName = $permission->name;
        }

        // Check for direct permissions
        if ($this->permissions()->where('name', $permissionName)->exists()) {
            return true;
        }

        // Check for permissions via roles
        foreach ($this->roles as $role) {
            if ($role->permissions()->where('name', $permissionName)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the user has any of the given permissions.
     *
     * @param array|string|Permission $permissions
     * @return bool
     */
    public function hasAnyPermission($permissions): bool
    {
        if (is_string($permissions)) {
            return $this->hasPermissionTo($permissions);
        }

        if ($permissions instanceof Permission) {
            return $this->hasPermissionTo($permissions);
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all permissions the user has via roles.
     *
     * @return array
     */
    public function getPermissionsViaRoles(): array
    {
        $permissionIds = [];

        foreach ($this->roles as $role) {
            $permissionIds = array_merge($permissionIds, $role->permissions->pluck('id')->toArray());
        }

        return array_unique($permissionIds);
    }

    /**
     * Get all permissions the user has, both directly and via roles.
     *
     * @return array
     */
    public function getAllPermissions(): array
    {
        $directPermissions = $this->permissions->pluck('id')->toArray();
        $rolePermissions = $this->getPermissionsViaRoles();

        return array_unique(array_merge($directPermissions, $rolePermissions));
    }

    /**
     * Get the user's branch ID.
     */
    public function getBranchId()
    {
        return $this->branch_id;
    }

    /**
     * Check if user belongs to a specific branch.
     */
    public function belongsToBranch($branchId)
    {
        return $this->branch_id === $branchId;
    }

    /**
     * Get the student associated with the user.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Check if user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->roles->first()->name === 'admin';
    }

    /**
     * Check if user is a teacher.
     *
     * @return bool
     */
    public function isTeacher(): bool
    {
       
        return $this->roles->first()->name === 'teacher';
    }

    /**
     * Check if user is a student.
     *
     * @return bool
     */
    public function isStudent(): bool
    {
        return $this->roles->first()->name === 'student';
    }
}
