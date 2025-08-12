<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Branch;
use App\Models\User;
use App\Rules\UserBranchRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends AdminController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view-teachers')->only(['index', 'show']);
        $this->middleware('permission:create-teachers')->only(['create', 'store']);
        $this->middleware('permission:edit-teachers')->only(['edit', 'update']);
        $this->middleware('permission:delete-teachers')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $teachers = Teacher::with(['branch'])
                        
                         ->paginate(15);

        // بيانات الفلاتر (ستتم تصفيتها تلقائياً بواسطة Global Scopes)
        $branches = Branch::all();
        
        // Get users with TEACHER role who are in the same branch as the current user
        $query = User::whereHas('roles', function($query) {
            $query->where('name', 'teacher');
        });
       
        // إذا كان المستخدم الحالي ليس super_admin، نقوم بتصفية المستخدمين حسب الفرع
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('branch_id', auth()->user()->branch_id);
        }
        
        $teacherUsers = $query->get();
        
        return view('admin.teachers.index', compact('teachers', 'branches', 'teacherUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // بيانات النموذج (ستتم تصفيتها تلقائياً بواسطة Global Scopes)
        $branches = Branch::all();

        return view('admin.teachers.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:teachers'],
            'phone' => ['required', 'string', 'max:20'],
            'specialization' => ['required', 'string', 'max:100'],
            'qualifications' => ['nullable', 'string', 'max:255'],
            'join_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'branch_id' => ['required', new UserBranchRule()],
        ]);

        Teacher::create($validated);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم إضافة المعلم بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $teacher->load([
            'branch',
            'groups' => function($query) {
                $query->withCount('students');
            },
            'students' => function($query) {
                $query->withCount(['grades', 'attendances']);
            }
        ]);

        // إحصائيات المدرس
        $stats = [
            'attendance_rate' => $teacher->students()->withCount([
                'attendances as present_count' => function($query) {
                    $query->where('status', 'present');
                }
            ])->get()->avg('present_count') ?? 0,
            'average_grade' => $teacher->students()->withAvg('grades', 'grade')->first()->grades_avg_grade_value??0
        ];

        return view('admin.teachers.show', compact('teacher', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $teacher = Teacher::whereHas('branch', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);
        
        $branches = $this->getUserBranches();
        
        return view('admin.teachers.edit', compact('teacher', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::whereHas('branch', function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:teachers,email,' . $id],
            'phone' => ['required', 'string', 'max:20'],
            'specialization' => ['required', 'string', 'max:100'],
            'qualifications' => ['nullable', 'string', 'max:255'],
            'join_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'branch_id' => ['required', new UserBranchRule()],
        ]);

        $teacher->update($validated);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم تحديث بيانات المعلم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = Teacher::whereHas('branch', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        $teacher->delete();

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }

    /**
     * Assign a user to a teacher
     */
    public function assignUser(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Update the teacher with the user_id
        $teacher->user_id = $validated['user_id'];
        $teacher->save();

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم ربط المستخدم بالمعلم بنجاح');
    }
}
