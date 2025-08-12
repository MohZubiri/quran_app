<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Rules\UserBranchRule;
use Illuminate\Support\Facades\Log;

class StudentController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view-students')->only(['index', 'show']);
        $this->middleware('permission:create-students')->only(['create', 'store']);
        $this->middleware('permission:edit-students')->only(['edit', 'update']);
        $this->middleware('permission:delete-students')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $students = Student::with(['group.branch', 'group.teacher'])
                         ->withCount(['grades', 'attendances'])
                         ->paginate(15);

        // بيانات الفلاتر (ستتم تصفيتها تلقائياً بواسطة Global Scopes)
        $branches = Branch::all();
        $teachers = Teacher::all();
        $groups = Group::all();
                         
        return view('admin.students.index', compact('students', 'branches', 'teachers', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // بيانات النموذج (ستتم تصفيتها تلقائياً بواسطة Global Scopes)
        $branches = Branch::all();
        $teachers = Teacher::all();
        $groups = Group::all();

        return view('admin.students.create', compact('branches', 'teachers', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'email', 'max:255', 'unique:students'],
                'phone' => ['required', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:255'],
                'birth_date' => ['required', 'date'],
                'parent_phone' => ['nullable', 'string', 'max:20'],
                'branch_id' => ['required', new UserBranchRule()],
                'group_id' => ['nullable', 'exists:groups,id'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

            $student = Student::create($validated);

            return redirect()->route('admin.students.index')
                ->with('success', 'تم إضافة الطالب بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating student: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء حفظ بيانات الطالب: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $student->load([
            'group.branch', 
            'group.teacher', 
            'grades' => function($query) {
                $query->latest('date')->limit(10);
            },
            'attendances' => function($query) {
                $query->latest('date')->limit(10);
            },
            'progressLogs' => function($query) {
                $query->latest('date')->limit(10);
            }
        ]);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $branches = Branch::all();
        $groups = Group::all();
        return view('admin.students.edit', compact('student', 'branches', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $student = Student::whereHas('branch', function ($query) {
                $query->where('user_id', auth()->id());
            })->findOrFail($id);

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'email', 'max:255', 'unique:students,email,' . $id],
                'phone' => ['required', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:255'],
                'birth_date' => ['required', 'date'],
                'parent_phone' => ['nullable', 'string', 'max:20'],
                'branch_id' => ['required', new UserBranchRule()],
                'group_id' => ['nullable', 'exists:groups,id'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

            $student->update($validated);

            return redirect()->route('admin.students.index')
                ->with('success', 'تم تحديث بيانات الطالب بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث بيانات الطالب: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        // Delete related records
        $student->enrollments()->delete();
        $student->grades()->delete();
        $student->attendances()->delete();
        
        // Delete the student
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'تم حذف الطالب بنجاح');
    }

    /**
     * Display the student's progress.
     */
    public function progress(Student $student)
    {
        $student->load(['grades', 'attendances', 'progressLogs']);
        return view('admin.students.progress', compact('student'));
    }

    /**
     * Create a user account for the student.
     */
    public function createAccount(Request $request, Student $student)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        // تحقق من عدم وجود حساب سابق
        if ($student->user_id) {
            return back()->with('error', 'الطالب لديه حساب مسبقاً');
        }

        // إنشاء بريد إلكتروني افتراضي
        $email = strtolower(
            transliterator_transliterate(
                'Any-Latin; Latin-ASCII; Lower()',
                $student->name 
            )
        ) . '@student.quran.com';

        // إزالة الأحرف غير المسموح بها في البريد الإلكتروني
        $email = preg_replace('/[^a-z0-9.@]/', '', $email);

        // التحقق من عدم وجود البريد الإلكتروني مسبقاً
        $count = 1;
        $originalEmail = $email;
        while (User::where('email', $email)->exists()) {
            $email = str_replace('@', $count . '@', $originalEmail);
            $count++;
        }

        if(!User::where('phone', $student->phone)->where('role', 'student')->exists()) {
            try {
                \DB::beginTransaction();
                
                // إنشاء حساب المستخدم
                $user = User::create([
                    'name' => $student->name,
                    'email' => $email,
                    'phone' => $student->phone,
                    'password' => bcrypt($request->password),
                    'status' => 'active',
                    'role' => 'student',
                    'branch_id' => $student->branch_id,
                ]);

                // ربط الحساب بالطالب
                $student->update(['user_id' => $user->id]);

                // تعيين دور الطالب
                $role = Role::where('name', 'student')->first();
                if (!$role) {
                    throw new \Exception('دور الطالب غير موجود في النظام');
                }
                
                $user->assignRole($role->id);

                \DB::commit();
                
                return back()->with('success', 'تم إنشاء حساب للطالب بنجاح. برقم هاتف: ' . $student->phone);
            } catch (\Exception $e) {
                \DB::rollback();
                return back()->with('error', 'حدث خطأ أثناء إنشاء الحساب: ' . $e->getMessage());
            }
        } else {
            return back()->with('error', 'هذا الطالب لديه مستخدم مسبقاً');
        }
    }
}
