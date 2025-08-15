<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Branch;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;
use App\Rules\UserBranchRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view-enrollments')->only(['index', 'show']);
        $this->middleware('permission:create-enrollments')->only(['create', 'store']);
        $this->middleware('permission:edit-enrollments')->only(['edit', 'update']);
        $this->middleware('permission:delete-enrollments')->only('destroy');
    }

    /**
     * Display a listing of enrollments.
     */
    public function index()
    {
        // Use withoutGlobalScopes on the student relationship to avoid StudentScope filtering
        $query = Enrollment::with([
            'student' => function($query) {
                $query->withoutGlobalScopes();
            },
            'group.branch'
        ]);

        // Get all branches
        $branches = Branch::all();
        $defaultBranch = null;
        
        // If there's only one branch, use it as the default filter
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
            $query->whereHas('group', function($q) use ($defaultBranch) {
                $q->where('branch_id', $defaultBranch->id);
            });
        }
        // Otherwise, apply branch filter if provided
        elseif (request('branch_id')) {
            $query->whereHas('group', function($q) {
                $q->where('branch_id', request('branch_id'));
            });
        }

        if (request('group_id')) {
            $query->where('group_id', request('group_id'));
        }

        if (request('status')) {
            $query->where('status', request('status'));
        } else {
            // Default to showing active enrollments if no status filter is applied
            $query->where('status', 'active');
        }

        $enrollments = $query->latest()->paginate(15);

        // Calculate statistics
        $statsQuery = Enrollment::query();

        $totalCount = $statsQuery->count();

        // Calculate statistics by status
        $statuses = ['active', 'inactive'];
        $byStatus = [];

        foreach ($statuses as $status) {
            $statusQuery = clone $statsQuery;
            $statusQuery->where('status', $status);

            $byStatus[$status] = [
                'count' => $statusQuery->count(),
                'percentage' => $totalCount > 0 ? round(($statusQuery->count() / $totalCount) * 100, 2) : 0
            ];
        }

        $stats = [
            'total' => $totalCount,
            'by_status' => $byStatus
        ];

        // We already have branches from earlier in the method
        $groups = Group::get();

        return view('admin.enrollments.index', compact('enrollments', 'branches', 'groups', 'stats', 'defaultBranch'));
    }

    /**
     * Show the form for creating a new enrollment.
     */
    public function create()
    {
        // Get branches accessible to the current user
        $branches = Branch::get();
        
        // If there's only one branch, set it as default
        $defaultBranch = null;
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        }

        // Get groups from these branches
        $groups = Group::get();

        // Get students who don't have active enrollments
        // We need to include branch information for filtering
        $students = Student::whereDoesntHave('enrollments', function($query) {
            $query->where('status', 'active');
        })->get();

        return view('admin.enrollments.create', compact('branches', 'groups', 'students', 'defaultBranch'));
    }

    /**
     * Store a newly created enrollment in storage.
     */
    public function store(Request $request)
    {
        // Get all branches
        $branches = Branch::all();
        
        // If there's only one branch, ensure it's used
        if ($branches->count() === 1) {
            $request->merge(['branch_id' => $branches->first()->id]);
        }
        
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id', new UserBranchRule()],
            'student_id' => ['required', 'exists:students,id'],
            'group_id' => ['required', 'exists:groups,id'],
            'enrollment_date' => ['required', 'date'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // التحقق من أن المجموعة تنتمي إلى نفس فرع المستخدم
        $group = Group::findOrFail($validated['group_id']);

        // التحقق من أن الطالب تنتمي إلى نفس فرع المستخدم
        $student = Student::findOrFail($validated['student_id']);

        // التحقق من عدم وجود تسجيل سابق للطالب في نفس المجموعة
        $existingEnrollment = Enrollment::where('student_id', $validated['student_id'])
            ->where('group_id', $validated['group_id'])
            ->where('status', 'active')
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'الطالب مسجل بالفعل في هذه المجموعة']);
        }

        // إنشاء التسجيل الجديد
        $enrollment = Enrollment::create($validated);

        // إذا كان التسجيل نشطًا، قم بتحديث حقل group_id في جدول الطلاب
        if ($validated['status'] === 'active') {
            $student->update(['group_id' => $validated['group_id']]);
        }

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'تم تسجيل الطالب في المجموعة بنجاح');
    }

    /**
     * Show the form for editing the specified enrollment.
     */
    public function edit(Enrollment $enrollment)
    {
        // الحصول على الفروع المتاحة للمستخدم الحالي
        $branches = Branch::get();
        
        // إذا كان هناك فرع واحد فقط، سيتم تعيينه تلقائياً
        $defaultBranch = null;
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        }

        // الحصول على المجموعات من هذه الفروع
        $groups = Group::get();

        // الحصول على الطالب لهذا التسجيل
        $student = $enrollment->student;

        return view('admin.enrollments.edit', compact('enrollment', 'branches', 'groups', 'student', 'defaultBranch'));
    }

    /**
     * Update the specified enrollment in storage.
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        // Get all branches
        $branches = Branch::all();
        
        // If there's only one branch, ensure it's used
        if ($branches->count() === 1) {
            $request->merge(['branch_id' => $branches->first()->id]);
        }

        $validated = $request->validate([
            'group_id' => [
                'required',
                'exists:groups,id',
                function($attribute, $value, $fail) {
                    $group = Group::find($value);
                    if (!$group || $group->branch->id !== auth()->user()->branch_id) {
                        $fail('المجموعة المحددة غير صالحة.');
                    }
                }
            ],
            'branch_id' => ['required', 'exists:branches,id', new UserBranchRule()],
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:500',
        ]);

        // التحقق من أن الطالب ليس لديه تسجيل نشط آخر
        if ($request->status === 'active') {
            $existingEnrollment = Enrollment::where('student_id', $enrollment->student_id)
                ->where('id', '!=', $enrollment->id)
                ->where('status', 'active')
                ->first();

            if ($existingEnrollment) {
                return back()->withErrors(['status' => 'الطالب مسجل بالفعل في مجموعة أخرى.'])->withInput();
            }

            // إذا كان التسجيل نشطًا، قم بتحديث حقل group_id في جدول الطلاب
            if ($enrollment->student) {
                $enrollment->student->update(['group_id' => $validated['group_id']]);
            }
        }

        $enrollment->update($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'تم تحديث التسجيل بنجاح');
    }

    /**
     * Remove the specified enrollment from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
       // $this->authorize('delete', $enrollment);

        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'تم حذف التسجيل بنجاح');
    }
}
