<?php

namespace App\Http\Controllers\Admin;

use App\Models\Group;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;

class GroupController extends AdminController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view-groups')->only(['index', 'show']);
        $this->middleware('permission:create-groups')->only(['create', 'store']);
        $this->middleware('permission:edit-groups')->only(['edit', 'update']);
        $this->middleware('permission:delete-groups')->only('destroy');
    }

    /**
     * Display a listing of the groups.
     */
    public function index()
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $groups = Group::with(['branch', 'teacher'])
                    
                      ->paginate(15);

        // بيانات الفلاتر (ستتم تصفيتها تلقائياً بواسطة Global Scopes)
        $branches = Branch::all();
        $teachers = Teacher::all();
        $subjects = Subject::all();
                      
        return view('admin.groups.index', compact('groups', 'branches', 'teachers', 'subjects'));
    }

    /**
     * Show the form for creating a new group.
     */
    public function create()
    {
        // بيانات النموذج (ستتم تصفيتها تلقائياً بواسطة Global Scopes)
        $branches = Branch::all();
        $teachers = Teacher::all();
        $subjects = Subject::all();

        // إذا كان المستخدم مدرس، نختار معرفه تلقائياً
        $preselectedTeacher = null;
        if (auth()->user()->hasRole('teacher')) {
            $preselectedTeacher = auth()->user()->teacher->id;
        }

        return view('admin.groups.create', compact('branches', 'teachers', 'subjects', 'preselectedTeacher'));
    }

    /**
     * Store a newly created group in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'branch_id' => ['required', 'exists:branches,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'schedule' => ['required', 'string'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
           
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $group = Group::create($validated);

        return redirect()->route('admin.groups.show', $group)
            ->with('success', 'تم إنشاء المجموعة بنجاح');
    }

    /**
     * Display the specified group.
     */
    public function show(Group $group)
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $group->load([
            'branch', 
            'teacher',
            'students' => function($query) {
                $query->withCount(['grades', 'attendances']);
            }
        ]);

        // إحصائيات المجموعة
        $stats = [
            'attendance_rate' => $group->students()->withCount([
                'attendances as present_count' => function($query) {
                    $query->where('status', 'present');
                }
            ])->get()->avg('present_count'),
            'average_grade' => $group->students()->withAvg('grades', 'grade')->first()->grades_avg_grade_value??0
        ];

        return view('admin.groups.show', compact('group', 'stats'));
    }

    /**
     * Show the form for editing the specified group.
     */
    public function edit(Group $group)
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $group->load(['branch', 'teacher']);

        // بيانات النموذج (ستتم تصفيتها تلقائياً بواسطة Global Scopes)
        $branches = Branch::all();
        $teachers = Teacher::all();
        $subjects = Subject::all();

        return view('admin.groups.edit', compact('group', 'branches', 'teachers', 'subjects'));
    }

    /**
     * Update the specified group in storage.
     */
    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'branch_id' => ['required', 'exists:branches,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'schedule' => ['required', 'json'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $group->update($validated);

        return redirect()->route('admin.groups.show', $group)
            ->with('success', 'تم تحديث المجموعة بنجاح');
    }

    /**
     * Remove the specified group from storage.
     */
    public function destroy(Group $group)
    {
        // التحقق من عدم وجود طلاب في المجموعة
        if ($group->students()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المجموعة لأنها تحتوي على طلاب');
        }

        $group->delete();

        return redirect()->route('admin.groups.index')
            ->with('success', 'تم حذف المجموعة بنجاح');
    }

    /**
     * Display the group's students.
     */
    public function students(Group $group)
    {

        $group->load(['students', 'branch', 'teacher', 'subject']);
        return response()->json($group);
       // return view('admin.groups.students', compact('group'));
    }

    /**
     * Display the group's attendance.
     */
    public function attendance(Group $group)
    {
        $group->load(['students', 'attendances' => function($query) {
            $query->orderBy('date', 'desc');
        }]);
        return view('admin.groups.attendance', compact('group'));
    }

    /**
     * Display the group's grades.
     */
    public function grades(Group $group)
    {
        $group->load(['students', 'grades' => function($query) {
            $query->orderBy('date', 'desc');
        }]);
        return view('admin.groups.grades', compact('group'));
    }
}
