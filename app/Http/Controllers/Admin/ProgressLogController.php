<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProgressLog;
use App\Models\Student;
use App\Models\Group;
use App\Models\Branch;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProgressLogController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view-progress-logs')->only(['index', 'show']);
        $this->middleware('permission:create-progress-logs')->only(['create', 'store']);
        $this->middleware('permission:edit-progress-logs')->only(['edit', 'update']);
        $this->middleware('permission:delete-progress-logs')->only('destroy');
    }

    /**
     * Display a listing of progress logs.
     */
    public function index()
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $progressLogs = ProgressLog::with(['student.group.branch', 'student.group.teacher'])
            ->latest('date')
            ->paginate(15);

        // بيانات الفلاتر (ستتم تصفيتها تلقائياً بواسطة Global Scopes)
        $branches = Branch::all();
        $teachers = Teacher::all();
        $groups = Group::all();
        $students = Student::all();
        
        return view('admin.progress-logs.index', compact('progressLogs', 'branches', 'teachers', 'groups', 'students'));
    }

    /**
     * Show the form for creating a new progress log.
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
        
        // التحقق من وجود فرع واحد فقط
        $defaultBranch = null;
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        }

        return view('admin.progress-logs.create', compact('branches', 'teachers', 'groups', 'students', 'preselectedTeacher', 'defaultBranch'));
    }

    /**
     * Store a newly created progress log in storage.
     */
    public function store(Request $request)
    {
        // التحقق من وجود فرع واحد فقط
        $branches = Branch::all();
        if ($branches->count() === 1) {
            // إذا كان هناك فرع واحد فقط، نضيفه للطلب
            $request->merge(['branch_id' => $branches->first()->id]);
        }
        
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'group_id' => 'required|exists:groups,id',
            'branch_id' => ['required', 'exists:branches,id', new \App\Rules\UserBranchRule],
            'date' => 'required|date',
            'surah_name' => 'required|string|max:50',
            'from_verse' => 'required|integer|min:1',
            'to_verse' => 'required|integer|min:1|gte:from_verse',
            'memorization_quality' => 'required|integer|min:1|max:10',
            'tajweed_quality' => 'required|integer|min:1|max:10',
            'revision_quality' => 'nullable|integer|min:1|max:10',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:excellent,good,needs_improvement,incomplete',
        ]);

        // Check if student is enrolled in the group
        $isEnrolled = $request->user()->students()
            ->where('student_id', $validated['student_id'])
            ->whereHas('enrollments', function($query) use ($validated) {
                $query->where('group_id', $validated['group_id'])
                    ->where('status', 'active');
            })
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('admin.progress-logs.create')
                ->with('error', 'الطالب غير مسجل في هذه المجموعة');
        }

        ProgressLog::create($validated);

        return redirect()->route('admin.progress-logs.index')
            ->with('success', 'تم تسجيل التقدم بنجاح');
    }

    /**
     * Display the specified progress log.
     */
    public function show(ProgressLog $progressLog)
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $progressLog->load(['student.group.branch', 'student.group.teacher']);

        // سجل تقدم الطالب
        $studentLogs = ProgressLog::where('student_id', $progressLog->student_id)
            ->where('id', '!=', $progressLog->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.progress-logs.show', compact('progressLog', 'studentLogs'));
    }

    /**
     * Show the form for editing the specified progress log.
     */
    public function edit(ProgressLog $progressLog)
    {
        $students = Student::whereHas('enrollments', function($query) {
            $query->where('status', 'active');
        })->get();
        $groups = Group::all();
        $branches = Branch::all();
        
        // التحقق من وجود فرع واحد فقط
        $defaultBranch = null;
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        }
        
        return view('admin.progress-logs.edit', compact('progressLog', 'students', 'groups', 'branches', 'defaultBranch'));
    }

    /**
     * Update the specified progress log in storage.
     */
    public function update(Request $request, ProgressLog $progressLog)
    {
        // التحقق من وجود فرع واحد فقط
        $branches = Branch::all();
        if ($branches->count() === 1) {
            // إذا كان هناك فرع واحد فقط، نضيفه للطلب
            $request->merge(['branch_id' => $branches->first()->id]);
        }
        
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'group_id' => 'required|exists:groups,id',
            'branch_id' => ['required', 'exists:branches,id', new \App\Rules\UserBranchRule],
            'date' => 'required|date',
            'surah_name' => 'required|string|max:50',
            'from_verse' => 'required|integer|min:1',
            'to_verse' => 'required|integer|min:1|gte:from_verse',
            'memorization_quality' => 'required|integer|min:1|max:10',
            'tajweed_quality' => 'required|integer|min:1|max:10',
            'revision_quality' => 'nullable|integer|min:1|max:10',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:excellent,good,needs_improvement,incomplete',
        ]);

        // Check if student is enrolled in the group
        $isEnrolled = $request->user()->students()
            ->where('student_id', $validated['student_id'])
            ->whereHas('enrollments', function($query) use ($validated) {
                $query->where('group_id', $validated['group_id'])
                    ->where('status', 'active');
            })
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('admin.progress-logs.edit', $progressLog)
                ->with('error', 'الطالب غير مسجل في هذه المجموعة');
        }

        $progressLog->update($validated);

        return redirect()->route('admin.progress-logs.index')
            ->with('success', 'تم تحديث سجل التقدم بنجاح');
    }

    /**
     * Remove the specified progress log from storage.
     */
    public function destroy(ProgressLog $progressLog)
    {
        $progressLog->delete();

        return redirect()->route('admin.progress-logs.index')
            ->with('success', 'تم حذف سجل التقدم بنجاح');
    }
}
