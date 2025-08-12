<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view-attendance')->only(['index', 'show']);
        $this->middleware('permission:create-attendance')->only(['create', 'store', 'storeBulk']);
        $this->middleware('permission:edit-attendance')->only(['edit', 'update']);
        $this->middleware('permission:delete-attendance')->only('destroy');
    }

    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request)
    {
        // Start with a base query
        $query = Attendance::with(['student.group.branch', 'student.group.teacher']);

        // Apply filters based on request parameters
        if ($request->filled('branch_id')) {
            $query->whereHas('student.group', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Get the filtered attendance records
        $attendance = $query->latest('date')->paginate(15)->withQueryString();

        // Calculate attendance statistics based on the filtered query
        $filteredQuery = clone $query;
        $total = $filteredQuery->count();
        
        $stats = [
            'total' => $total,
            'by_status' => [
                'present' => [
                    'count' => $filteredQuery->clone()->where('status', 'present')->count(),
                    'percentage' => $total > 0 ? round(($filteredQuery->clone()->where('status', 'present')->count() / $total) * 100, 1) : 0
                ],
                'absent' => [
                    'count' => $filteredQuery->clone()->where('status', 'absent')->count(),
                    'percentage' => $total > 0 ? round(($filteredQuery->clone()->where('status', 'absent')->count() / $total) * 100, 1) : 0
                ],
                'late' => [
                    'count' => $filteredQuery->clone()->where('status', 'late')->count(),
                    'percentage' => $total > 0 ? round(($filteredQuery->clone()->where('status', 'late')->count() / $total) * 100, 1) : 0
                ],
                'excused' => [
                    'count' => $filteredQuery->clone()->where('status', 'excused')->count(),
                    'percentage' => $total > 0 ? round(($filteredQuery->clone()->where('status', 'excused')->count() / $total) * 100, 1) : 0
                ]
            ]
        ];

        // Get data for filters (will be automatically filtered by Global Scopes)
        $branches = Branch::all();
        $teachers = Teacher::all();
        $groups = Group::all();
        $students = Student::all();
                               
        return view('admin.attendance.index', compact('attendance', 'branches', 'teachers', 'groups', 'students', 'stats'));
    }

    /**
     * Show the form for creating a new attendance record.
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

        return view('admin.attendance.create', compact('branches', 'teachers', 'groups', 'students', 'preselectedTeacher'));
    }

    /**
     * Store a newly created attendance record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'group_id' => 'required|exists:groups,id',
            'teacher_id' => 'required|exists:teachers,id',
            'date' => 'required|date',
            'students' => 'required|array',
            'students.*.status' => 'required|in:present,absent,late,excused',
            'students.*.notes' => 'nullable|string|max:1000'
        ]);

        // Get the date in the correct format
        $date = Carbon::parse($validated['date']);

        // Check if any attendance records already exist for this group and date
        $exists = Attendance::where('group_id', $validated['group_id'])
            ->whereDate('date', $date)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.attendance.create')
                ->with('error', 'يوجد سجل حضور مسبق لهذه الحلقة في هذا اليوم');
        }
     // Create attendance records for each student;
        foreach ($request->students as $studentId => $data) {
            Attendance::create([
                'student_id' => $studentId,
                'group_id' => $validated['group_id'],
                'teacher_id' => $validated['teacher_id'],
                'date' => $date,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null
            ]);
        }

        return redirect()->route('admin.attendance.index')
            ->with('success', 'تم تسجيل الحضور بنجاح');
    }

    /**
     * Store multiple attendance records at once.
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes' => 'nullable|string|max:1000',
        ]);

        $group = Group::findOrFail($request->group_id);
        $date = Carbon::parse($request->date);

        // Check if any attendance records exist for this date and group
        $exists = Attendance::where('group_id', $group->id)
            ->whereDate('date', $date)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.attendance.create')
                ->with('error', 'يوجد سجلات حضور مسبقة لهذه المجموعة في هذا اليوم');
        }

        // Create attendance records
        foreach ($request->attendance as $record) {
            Attendance::create([
                'student_id' => $record['student_id'],
                'group_id' => $group->id,
                'date' => $date,
                'status' => $record['status'],
                'notes' => $record['notes'] ?? null,
            ]);
        }

        return redirect()->route('admin.attendance.index')
            ->with('success', 'تم تسجيل الحضور للمجموعة بنجاح');
    }

    /**
     * Display the specified attendance record.
     */
    public function show(Attendance $attendance)
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $attendance->load(['student.group.branch', 'student.group.teacher']);

        // سجل حضور الطالب
        $studentAttendances = Attendance::where('student_id', $attendance->student_id)
                                      ->where('id', '!=', $attendance->id)
                                      ->latest()
                                      ->limit(10)
                                      ->get();

        return view('admin.attendance.show', compact('attendance', 'studentAttendances'));
    }

    /**
     * Show the form for editing the specified attendance record.
     */
    public function edit(Attendance $attendance)
    {
        $branches = Branch::all();
        $teachers = Teacher::all();
        $groups = Group::all();
        $students = Student::all();
        return view('admin.attendance.edit', compact('attendance', 'groups', 'students', 'teachers', 'branches', 'students'));
    }

    /**
     * Update the specified attendance record in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'group_id' => 'required|exists:groups,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if another attendance record exists for the same date (excluding current record)
        $exists = Attendance::where('student_id', $validated['student_id'])
            ->where('group_id', $validated['group_id'])
            ->whereDate('date', Carbon::parse($validated['date']))
            ->where('id', '!=', $attendance->id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.attendance.edit', $attendance)
                ->with('error', 'يوجد سجل حضور مسبق لهذا الطالب في هذا اليوم');
        }

        $attendance->update($validated);

        return redirect()->route('admin.attendance.index')
            ->with('success', 'تم تحديث سجل الحضور بنجاح');
    }

    /**
     * Remove the specified attendance record from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'تم حذف سجل الحضور بنجاح');
    }
}
