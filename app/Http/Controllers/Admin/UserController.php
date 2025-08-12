<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Rules\UserBranchRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view-users')->only(['index', 'show']);
        $this->middleware('permission:create-users')->only(['create', 'store']);
        $this->middleware('permission:edit-users')->only(['edit', 'update']);
        $this->middleware('permission:delete-users')->only('destroy');
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // إذا كان المستخدم مدير نظام، يرى جميع المستخدمين
        if (auth()->user()->hasRole('super_admin')) {
            $users = User::paginate(15);
        } else {
            // غير ذلك، يرى فقط مستخدمي فرعه
            $users = User::where('branch_id', auth()->user()->branch_id)->paginate(15);
        }
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $branches = Branch::all();
        $roles = collect();
        
        if (auth()->user()->hasRole('super_admin')) {
            // المدير العام يرى جميع الأدوار
            $roles = Role::all();
        } else {
            // مدير الفرع يرى فقط الأدوار المحددة
            $roles = Role::whereIn('name', ['teacher', 'student', 'supervisor'])->get();
        }

        return view('admin.users.create', compact('branches', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'exists:roles,id'],
            'branch_id' => ['required', new UserBranchRule],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'branch_id' => $request->branch_id,
        ]);

        // تعيين الدور للمستخدم
        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $branches = Branch::all();
        $roles = collect();
        
        if (auth()->user()->hasRole('super_admin')) {
            // المدير العام يرى جميع الأدوار
            $roles = Role::all();
        } else {
            // مدير الفرع يرى فقط الأدوار المحددة
            $roles = Role::whereIn('name', ['teacher', 'student', 'supervisor'])->get();
        }

        return view('admin.users.edit', compact('user', 'branches', 'roles'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'exists:roles,id'],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'branch_id' => $request->branch_id,
           
        ];

        // تحديث كلمة المرور فقط إذا تم إدخالها
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            
            $userData['password'] = Hash::make($request->password);
        }

        // تحديث بيانات المستخدم
        $user->update($userData);

        // تحديث الدور للمستخدم
        $currentRole = $user->roles()->first();
        $newRoleId = $request->role;
        
        // إذا كان المستخدم لديه دور حالي وهو مختلف عن الدور الجديد
        // أو إذا لم يكن لديه دور حالي
        if ((!$currentRole && $newRoleId) || ($currentRole && $currentRole->id !== $newRoleId)) {
            // إزالة جميع الأدوار الحالية
          
            $user->roles()->detach();
            
            // إضافة الدور الجديد
            $user->assignRole($newRoleId);
          
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Prevent deleting the last admin user
        if ($user->isAdmin()) {
            $adminCount = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->count();
            
            if ($adminCount <= 1) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'لا يمكن حذف آخر مستخدم بصلاحيات المدير');
            }
        }

        // Remove role associations
        $user->roles()->detach();
        
        // Remove permission associations
        $user->permissions()->detach();
        
        // Delete the user
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }
}
