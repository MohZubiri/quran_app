<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use App\Rules\UserBranchRule;
use Illuminate\Support\Facades\DB;

class GradeController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view-grades')->only(['index', 'show']);
        $this->middleware('permission:create-grades')->only(['create', 'store']);
        $this->middleware('permission:edit-grades')->only(['edit', 'update']);
        $this->middleware('permission:delete-grades')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $grades = Grade::with(['student.group.branch', 'student.group.teacher'])
                      ->latest('date')
                      ->paginate(15);

        // بيانات الفلاتر (ستتم تصفيتها تلقائياً بواسطة Global Scopes)
        $branches = Branch::all();
        $teachers = Teacher::all();
        $groups = Group::all();
        $students = Student::all();
                      
        return view('admin.grades.index', compact('grades', 'branches', 'teachers', 'groups', 'students'));
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
        $students = Student::all();

        // إذا كان المستخدم مدرس، نختار معرفه تلقائياً
        $preselectedTeacher = null;
        if (auth()->user()->hasRole('teacher')) {
            $preselectedTeacher = auth()->user()->teacher->id;
        }

        return view('admin.grades.create', compact('branches', 'teachers', 'groups', 'students', 'preselectedTeacher'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'date' => ['required', 'date'],
            'grade_value' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'evaluation' => ['required', 'string', 'in:excellent,very_good,good,fair,poor'],
        ]);

        dd($validated);
        $grade = Grade::create($validated);

        return redirect()->route('admin.grades.show', $grade)
            ->with('success', 'تم إضافة الدرجة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Grade $grade)
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $grade->load(['student.group.branch', 'student.group.teacher']);

        // بيانات إضافية للعرض
        $studentGrades = Grade::where('student_id', $grade->student_id)
                            ->where('id', '!=', $grade->id)
                            ->latest()
                            ->limit(10)
                            ->get();

        return view('admin.grades.show', compact('grade', 'studentGrades'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grade $grade)
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $grade->load(['student.group.branch', 'student.group.teacher']);

        // بيانات النموذج (ستتم تصفيتها تلقائياً بواسطة Global Scopes)
        $branches = Branch::all();
        $teachers = Teacher::all();
        $groups = Group::all();
        $students = Student::all();

        return view('admin.grades.edit', compact('grade', 'branches', 'teachers', 'groups', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'date' => ['required', 'date'],
            'grade_value' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'evaluation' => ['required', 'string', 'in:excellent,very_good,good,fair,poor'],
        ]);

        $grade->update($validated);

        return redirect()->route('admin.grades.show', $grade)
            ->with('success', 'تم تحديث الدرجة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade)
    {
        $grade->delete();

        return redirect()->route('admin.grades.index')
            ->with('success', 'تم حذف الدرجة بنجاح');
    }
}
