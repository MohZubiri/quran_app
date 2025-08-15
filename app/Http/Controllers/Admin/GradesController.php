<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Rules\UniqueGradeRule;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-grades')->only(['index', 'show']);
        $this->middleware('permission:create-grades')->only(['create', 'store']);
        $this->middleware('permission:edit-grades')->only(['edit', 'update']);
        $this->middleware('permission:delete-grades')->only('destroy');
    }

    /**
     * Display a listing of grades.
     */
    public function index(Request $request)
    {
        // Build the query with filters
        $query = Grade::with(['student.group.branch', 'subject', 'teacher']);

        // Get all branches
        $branches = Branch::all();
        $defaultBranch = null;
        
        // If there's only one branch, use it as the default filter
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
            $query->whereHas('student.group', function($q) use ($defaultBranch) {
                $q->where('branch_id', $defaultBranch->id);
            });
        }
        // Otherwise, apply filters as normal
        else {
            if ($request->filled('branch_id')) {
                $query->whereHas('student.group', function($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            }
        }
        
        // Apply other filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('group_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Get filtered results and group by student, date, and grade type
        $grades = $query->orderBy('date', 'desc')
                       ->get()
                       ->groupBy(function($grade) {
                           return $grade->student_id . '_' . $grade->date->format('Y-m-d');
                       })
                       ->map(function ($dayGrades) {
                           // Group by grade type for this student and day
                           return [
                               'student' => $dayGrades->first()->student,
                               'date' => $dayGrades->first()->date->format('Y-m-d'),
                               'grades' => $dayGrades->groupBy('grade_type')
                                                   ->map(function($typeGrades) {
                                                       return $typeGrades->first();
                                                   })
                           ];
                       });
                  
        // Convert the collection to paginator
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $pagedData = $grades->forPage($currentPage, $perPage);
        $grades = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $grades->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => $request->query()]
        );

        // Calculate statistics based on filtered data
        $total = $query->count();
        
        // Calculate grade distribution
        $distribution = [
            'excellent' => ['count' => $query->clone()->where('grade', '>=', 90)->count()],
            'very_good' => ['count' => $query->clone()->whereBetween('grade', [80, 89])->count()],
            'good' => ['count' => $query->clone()->whereBetween('grade', [70, 79])->count()],
            'pass' => ['count' => $query->clone()->whereBetween('grade', [60, 69])->count()],
            'poor' => ['count' => $query->clone()->where('grade', '<', 60)->count()]
        ];

        // Calculate percentages
        foreach ($distribution as &$level) {
            $level['percentage'] = $total > 0 ? round(($level['count'] / $total) * 100, 1) : 0;
        }

        // Calculate type statistics
        $typeStats = [];
        foreach (['achievement', 'behavior', 'attendance', 'appearance', 'plan_score'] as $type) {
            $typeQuery = $query->clone()->where('grade_type', $type);
            $typeStats[$type] = [
                'count' => $typeQuery->count(),
                'average' => round($typeQuery->avg('grade') ?? 0, 1)
            ];
        }

        $stats = [
            'count' => $total,
            'average' => round($query->clone()->avg('grade') ?? 0, 1),
            'highest' => $query->clone()->max('grade') ?? 0,
            'lowest' => $query->clone()->min('grade') ?? 0,
            'distribution' => $distribution,
            'by_type' => $typeStats
        ];

        // Get filter options
        $branches = Branch::all();
        $students = Student::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $groups = Group::all();
        
        // If there's only one branch, set it as default
        $defaultBranch = null;
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        }

        return view('admin.grades.index', compact('grades', 'students', 'subjects', 'teachers', 'stats', 'branches', 'defaultBranch', 'groups'));
    }

    /**
     * Show the form for creating a new grade.
     */
    public function create(Request $request)
    {
        $groups = Group::all();
        $teachers = Teacher::all();
        $subjects = Subject::all();
        if (auth()->user()->hasRole('teacher')) {
            $preselectedTeacher = auth()->user()->teacher->id;
        }
       
        
        // التحقق من وجود فرع واحد فقط
        $branches = Branch::all();
        $defaultBranch = null;
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        }
        
        // إذا تم تحديد المجموعة في الرابط، نقوم بتحديد المعلم والمادة المرتبطين بها
        $preselectedGroup = null;
        $preselectedSubject = null;
        $preselectedTeacherFromGroup = null;
        
        // تحديد الطلاب المعروضين
        if ($request->has('group_id')) {
            $preselectedGroup = Group::find($request->group_id);
            if ($preselectedGroup) {
                // تحديد المعلم والمادة من المجموعة
                $preselectedSubject = $preselectedGroup->subject_id;
                $preselectedTeacherFromGroup = $preselectedGroup->teacher_id;
                
                // جلب الطلاب المنتمين لهذه المجموعة فقط
                $students = Student::where('group_id', $preselectedGroup->id)->get();
            } else {
                $students = Student::all();
            }
        } else {
            $students = Student::all();
        }

        return view('admin.grades.create', compact(
            'students', 'groups', 'teachers', 'subjects', 
            'preselectedTeacher', 'defaultBranch', 'preselectedGroup',
            'preselectedSubject', 'preselectedTeacherFromGroup'
        ));
    }

    /**
     * Store a newly created grade in storage.
     */
    public function store(Request $request)
    {
        try{
        // التحقق من وجود فرع واحد فقط
        $branches = Branch::all();
        if ($branches->count() === 1) {
            // إذا كان هناك فرع واحد فقط، نضيفه للطلب
            $request->merge(['branch_id' => $branches->first()->id]);
        }
        
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'group_id' => 'required|exists:groups,id',
            'teacher_id' => 'required|exists:teachers,id',
            'branch_id' => ['required', 'exists:branches,id', new \App\Rules\UserBranchRule],
            'date' => 'required|date',
            'grades' => 'required|array',
            'grades.*.grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.verses' => 'nullable|string|max:255',
            'grades.*.notes' => 'nullable|string|max:1000'
        ]);
    
        // Validate unique grade for each type
        foreach ($request->grades as $type => $gradeData) {
         
            if (!empty($gradeData['grade'])) {
                $uniqueRule = new UniqueGradeRule(
                    $request->student_id,
                    $request->subject_id,
                    $request->teacher_id,
                    $request->date,
                    $type
                );
           
                $request->validate([
                    "grades.{$type}.grade" => [$uniqueRule]
                ]);
            }
        }
      
        $grades = [];
        foreach ($request->grades as $type => $gradeData) {
            if (!empty($gradeData['grade'])) {
                $grades[] = Grade::create([
                    'student_id' => $request->student_id,
                    'group_id' => $request->group_id,
                    'subject_id' => $request->subject_id,
                    'teacher_id' => $request->teacher_id,
                    'grade_type' => $type,
                    'grade' => $gradeData['grade'],
                    'date' => $request->date,
                    'notes' => $gradeData['notes'] ?? null
                ]);
            }
        }
        }catch(\Exception $e){
            dd($e);
            return redirect()->route('admin.grades.index')->with('error', 'حدث خطأ أثناء إضافة التقييمات');
        }
        return redirect()->route('admin.grades.index')->with('success', 'تم إضافة التقييمات بنجاح');
    }

    /**
     * Display the specified grade.
     */
    public function show(Grade $grade)
    {
       
        // Get recent grades for the same student
        $studentGrades = Grade::where('student_id', $grade->student_id)
                            
                            ->get();

        return view('admin.grades.show', compact('grade', 'studentGrades'));
    }

    /**
     * Show the form for editing the specified grade.
     */
    public function edit(Grade $grade)
    {
        $students = Student::all();
        $groups = Group::all();
        $teachers = Teacher::all();
        $subjects = Subject::all();
        
        // التحقق من وجود فرع واحد فقط
        $branches = Branch::all();
        $defaultBranch = null;
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        }

        // Get all grades for this student on the same date
        $allGrades = Grade::where('student_id', $grade->student_id)
                         ->whereDate('date', $grade->date)
                         ->get()
                         ->keyBy('grade_type');

        return view('admin.grades.edit', compact('grade', 'students', 'groups', 'teachers', 'subjects', 'allGrades', 'defaultBranch'));
    }

    /**
     * Update the specified grade in storage.
     */
    public function update(Request $request, Grade $grade)
    {
        // التحقق من وجود فرع واحد فقط
        $branches = Branch::all();
        if ($branches->count() === 1) {
            // إذا كان هناك فرع واحد فقط، نضيفه للطلب
            $request->merge(['branch_id' => $branches->first()->id]);
        }
        
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'group_id' => 'required|exists:groups,id',
            'teacher_id' => 'required|exists:teachers,id',
            'branch_id' => ['required', 'exists:branches,id', new \App\Rules\UserBranchRule],
            'date' => 'required|date',
            'grades' => 'required|array',
            'grades.*.grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.notes' => 'nullable|string|max:1000'
        ]);

        // Get all grades for this student on this date
        $existingGrades = Grade::where('student_id', $grade->student_id)
                             ->whereDate('date', $grade->date)
                             ->get()
                             ->keyBy('grade_type');
                          
        // Update or create grades for each type
        foreach ($request->grades as $type => $gradeData) {
            if (isset($gradeData['grade']) && $gradeData['grade'] !== '') {
                
            
                $gradeModel = $existingGrades[$type] ?? new Grade([
                    'student_id' => $request->student_id,
                    'group_id' => $request->group_id,
                    'subject_id' => $request->subject_id,
                    'teacher_id' => $request->teacher_id,
                    'grade_type' => $type,
                    'date' => $request->date
                ]);
               
                $gradeModel->grade = $gradeData['grade'];
                $gradeModel->notes = $gradeData['notes'] ?? null;
                $gradeModel->save();
               
            }
        }

        return redirect()->route('admin.grades.index')->with('success', 'تم تحديث التقييمات بنجاح');
    }

    /**
     * Remove the specified grade from storage.
     */
    public function destroy(Grade $grade)
    {
        $grade->delete();

        return redirect()->route('admin.grades.index')
            ->with('success', 'تم حذف التقييم بنجاح');
    }
}
