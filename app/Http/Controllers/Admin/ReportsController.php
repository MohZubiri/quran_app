<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-reports');
    }

    /**
     * Display a listing of reports.
     */
    public function index()
    {
        // Get basic statistics for the reports index page
        $stats = [
            'total_students' => Student::count(),
            'attendance_rate' => 85, // Placeholder value - would calculate from actual data
            'average_grade' => 75,   // Placeholder value - would calculate from actual data
            'total_groups' => Group::count(),
        ];
        
        // Get chart data for the reports index page
        $charts = [
            'branches' => [
                'labels' => Branch::pluck('name')->toArray(),
                'data' => Branch::withCount('groups')->pluck('groups_count')->toArray(),
            ],
            'attendance' => [
                'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                'data' => [75, 82, 78, 85, 80, 88], // Placeholder data
            ],
            'grades' => [
                'data' => [45, 65, 85, 35, 15], // Placeholder data for grade distribution
            ],
            'progress' => [
                'labels' => ['الأسبوع 1', 'الأسبوع 2', 'الأسبوع 3', 'الأسبوع 4'],
                'memorization' => [65, 68, 72, 75],
                'tajweed' => [60, 63, 67, 70],
                'recitation' => [62, 65, 69, 73], // Added missing recitation data
            ],
        ];
        
        return view('admin.reports.index', compact('stats', 'charts'));
    }

    /**
     * Display the monthly report.
     */
    public function monthlyReport(Request $request)
    {
        // Get all branches
        $branches = Branch::all();
        $defaultBranch = null;
        
        // If there's only one branch, use it as the default filter
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        }
        
        // Get all groups
        $groups = Group::all();
        
        // Initialize empty report data
        $reportData = [];
        $summary = [];
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            // Build the query with filters
            $query = Grade::with(['student.group.branch', 'subject', 'teacher'])
                ->whereDate('date', '>=', $request->date_from)
                ->whereDate('date', '<=', $request->date_to);
            
            // Apply branch filter if set
            if ($request->filled('branch_id')) {
                $query->whereHas('student.group', function($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            } elseif ($defaultBranch) {
                $query->whereHas('student.group', function($q) use ($defaultBranch) {
                    $q->where('branch_id', $defaultBranch->id);
                });
            }
            
            // Apply group filter if set
            if ($request->filled('group_id')) {
                $query->whereHas('student', function($q) use ($request) {
                    $q->where('group_id', $request->group_id);
                });
            }
            
            // Get all grades for the period
            $grades = $query->get();
            
            // Group grades by student
            $studentGrades = $grades->groupBy('student_id');
            
            // Process each student's grades
            foreach ($studentGrades as $studentId => $gradeCollection) {
                $student = $gradeCollection->first()->student;
                
                // Initialize student data
                $studentData = [
                    'id' => $student->id,
                    'name' => $student->name,
                    'group' => $student->group->name,
                    'achievement' => 0,
                    'behavior' => 0,
                    'attendance' => 0,
                    'appearance' => 0,
                    'plan_score' => 0,
                    'achievement_count' => 0,
                    'behavior_count' => 0,
                    'attendance_count' => 0,
                    'appearance_count' => 0,
                    'plan_score_count' => 0,
                ];
                
                // Calculate averages for each grade type
                foreach ($gradeCollection as $grade) {
                    switch ($grade->grade_type) {
                        case 'achievement':
                            $studentData['achievement'] += $grade->grade;
                            $studentData['achievement_count']++;
                            break;
                        case 'behavior':
                            $studentData['behavior'] += $grade->grade;
                            $studentData['behavior_count']++;
                            break;
                        case 'attendance':
                            $studentData['attendance'] += $grade->grade;
                            $studentData['attendance_count']++;
                            break;
                        case 'appearance':
                            $studentData['appearance'] += $grade->grade;
                            $studentData['appearance_count']++;
                            break;
                        case 'plan_score':
                            $studentData['plan_score'] += $grade->grade;
                            $studentData['plan_score_count']++;
                            break;
                    }
                }
                
                // Calculate averages
                $studentData['achievement'] = $studentData['achievement_count'] > 0 ? 
                    round($studentData['achievement'] / $studentData['achievement_count'], 1) : 0;
                
                $studentData['behavior'] = $studentData['behavior_count'] > 0 ? 
                    round($studentData['behavior'] / $studentData['behavior_count'], 1) : 0;
                
                $studentData['attendance'] = $studentData['attendance_count'] > 0 ? 
                    round($studentData['attendance'] / $studentData['attendance_count'], 1) : 0;
                
                $studentData['appearance'] = $studentData['appearance_count'] > 0 ? 
                    round($studentData['appearance'] / $studentData['appearance_count'], 1) : 0;
                
                $studentData['plan_score'] = $studentData['plan_score_count'] > 0 ? 
                    round($studentData['plan_score'] / $studentData['plan_score_count'], 1) : 0;
                
                // Add to report data
                $reportData[$studentId] = $studentData;
            }
            
            // Calculate summary data
            if (count($reportData) > 0) {
                $summary = [
                    'total_students' => count($reportData),
                    'achievement_avg' => array_sum(array_column($reportData, 'achievement')) / count($reportData),
                    'behavior_avg' => array_sum(array_column($reportData, 'behavior')) / count($reportData),
                    'attendance_avg' => array_sum(array_column($reportData, 'attendance')) / count($reportData),
                    'appearance_avg' => array_sum(array_column($reportData, 'appearance')) / count($reportData),
                    'plan_score_avg' => array_sum(array_column($reportData, 'plan_score')) / count($reportData),
                ];
            }
        }
        
        return view('admin.reports.monthly', compact('branches', 'groups', 'defaultBranch', 'reportData', 'summary'));
    }
    
    /**
     * Display the students report.
     */
    public function studentsReport(Request $request)
    {
        // Get user's branches based on their role
        $user = auth()->user();
        $branches = $user->branches();
        $defaultBranch = null;
        
        // If user has only one branch, set it as default
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        } else if ($user->branch_id) {
            $defaultBranch = Branch::find($user->branch_id);
        }
        
        // Get groups based on branch filter
        if ($defaultBranch) {
            $groups = Group::where('branch_id', $defaultBranch->id)->get();
        } else {
            $groups = Group::all();
        }
        
        // Apply branch filter to statistics
        $studentQuery = Student::query();
        $groupQuery = Group::query();
        
        // Filter by branch if default branch is set
        if ($defaultBranch) {
            $studentQuery->whereHas('group', function($q) use ($defaultBranch) {
                $q->where('branch_id', $defaultBranch->id);
            });
            $groupQuery->where('branch_id', $defaultBranch->id);
        }
        
        // Get basic statistics for the reports index page
        $stats = [
            'total_students' => $studentQuery->count(),
            'attendance_rate' => 85, // Placeholder value - would calculate from actual data
            'average_grade' => 75,   // Placeholder value - would calculate from actual data
            'total_groups' => $groupQuery->count(),
        ];
        
        // Get chart data for the reports index page
        // If default branch is set, filter chart data by branch
        if ($defaultBranch) {
            $charts = [
                'branches' => [
                    'labels' => [$defaultBranch->name],
                    'data' => [Group::where('branch_id', $defaultBranch->id)->count()],
                ],
                'attendance' => [
                    'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                    'data' => [75, 82, 78, 85, 80, 88], // Placeholder data - would filter by branch in real implementation
                ],
                'grades' => [
                    'data' => [45, 65, 85, 35, 15], // Placeholder data - would filter by branch in real implementation
                ],
                'progress' => [
                    'labels' => ['الأسبوع 1', 'الأسبوع 2', 'الأسبوع 3', 'الأسبوع 4'],
                    'memorization' => [65, 68, 72, 75], // Placeholder data - would filter by branch in real implementation
                    'tajweed' => [60, 63, 67, 70], // Placeholder data - would filter by branch in real implementation
                ],
                'levels' => [25, 30, 20, 15, 10], // Placeholder data - would filter by branch in real implementation
                'grade_types' => [78, 72, 75, 85], // Placeholder data - would filter by branch in real implementation
            ];
        } else {
            $charts = [
                'branches' => [
                    'labels' => Branch::pluck('name')->toArray(),
                    'data' => Branch::withCount('groups')->pluck('groups_count')->toArray(),
                ],
                'attendance' => [
                    'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                    'data' => [75, 82, 78, 85, 80, 88], // Placeholder data
                ],
                'grades' => [
                    'data' => [45, 65, 85, 35, 15], // Placeholder data for grade distribution
                ],
                'progress' => [
                    'labels' => ['الأسبوع 1', 'الأسبوع 2', 'الأسبوع 3', 'الأسبوع 4'],
                    'memorization' => [65, 68, 72, 75],
                    'tajweed' => [60, 63, 67, 70],
                ],
                'levels' => [25, 30, 20, 15, 10], // Placeholder data for student levels distribution
                'grade_types' => [78, 72, 75, 85], // Placeholder data for grade types
            ];
        }
        
        // Placeholder for student data
        $students = [];
        
        // Update overview data based on branch filter
        if ($defaultBranch) {
            $overview = [
                'total_students' => $studentQuery->count(),
                'avg_attendance' => 85, // Placeholder - would calculate from filtered data
                'avg_grades' => 75,     // Placeholder - would calculate from filtered data
                'total_grades' => 1250, // Placeholder - would calculate from filtered data
            ];
        } else {
            $overview = [
                'total_students' => Student::count(),
                'avg_attendance' => 85,
                'avg_grades' => 75,
                'total_grades' => 1250,
            ];
        }
        
        $recommendations = [
            'top_students' => [],
            'needs_improvement' => [],
            'general' => [
                'يوصى بزيادة التركيز على تحسين مهارات التجويد للطلاب',
                'متابعة الطلاب ذوي المستوى المنخفض بشكل فردي',
                'تنظيم مسابقات دورية لتحفيز الطلاب على التحسن'
            ]
        ];
        
        return view('admin.reports.students', compact('stats', 'charts', 'branches', 'groups', 'students', 'overview', 'recommendations', 'defaultBranch'));
    }

    /**
     * Display the attendance summary report.
     */
    public function attendanceSummary(Request $request)
    {
        // Get all branches
        $branches = Branch::all();
        $defaultBranch = null;
        
        // If there's only one branch, use it as the default filter
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        }
        
        // Get all groups
        $groups = Group::all();
        
        // Initialize empty report data
        $reportData = [];
        $summary = [];
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            // Build the query with filters
            $query = Attendance::with(['student.group.branch', 'teacher'])
                ->whereDate('date', '>=', $request->date_from)
                ->whereDate('date', '<=', $request->date_to);
            
            // Apply branch filter if set
            if ($request->filled('branch_id')) {
                $query->whereHas('student.group', function($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
            } elseif ($defaultBranch) {
                $query->whereHas('student.group', function($q) use ($defaultBranch) {
                    $q->where('branch_id', $defaultBranch->id);
                });
            }
            
            // Apply group filter if set
            if ($request->filled('group_id')) {
                $query->whereHas('student', function($q) use ($request) {
                    $q->where('group_id', $request->group_id);
                });
            }
            
            // Get attendance data
            $attendances = $query->get();
            
            // Process attendance data
            // This is a placeholder - actual implementation would depend on your attendance model structure
            $summary = [
                'total_records' => $attendances->count(),
                'present_count' => $attendances->where('status', 'present')->count(),
                'absent_count' => $attendances->where('status', 'absent')->count(),
                'late_count' => $attendances->where('status', 'late')->count(),
                'excused_count' => $attendances->where('status', 'excused')->count(),
                'attendance_rate' => $attendances->count() > 0 ? 
                    ($attendances->where('status', 'present')->count() / $attendances->count()) * 100 : 0,
            ];
        }
        
        return view('admin.reports.attendance_summary', compact('branches', 'groups', 'defaultBranch', 'reportData', 'summary'));
    }
    
    /**
     * Display the grades summary report.
     */
    public function gradesSummary(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.grades_summary');
    }

    /**
     * Display the student progress report.
     */
    public function studentProgressReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.student_progress');
    }

    /**
     * Display the student comparison report.
     */
    public function studentComparisonReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.student_comparison');
    }

    /**
     * Display the attendance report.
     */
    public function attendanceReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.attendance');
    }

    /**
     * Display the attendance by group report.
     */
    public function attendanceByGroupReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.attendance_by_group');
    }

    /**
     * Display the attendance trends report.
     */
    public function attendanceTrendsReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.attendance_trends');
    }

    /**
     * Display the grades report.
     */
    public function gradesReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.grades');
    }

    /**
     * Display the grades by subject report.
     */
    public function gradesBySubjectReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.grades_by_subject');
    }

    /**
     * Display the grades by teacher report.
     */
    public function gradesByTeacherReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.grades_by_teacher');
    }

    /**
     * Display the performance report.
     */
    public function performanceReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.performance');
    }

    /**
     * Display the performance by branch report.
     */
    public function performanceByBranchReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.performance_by_branch');
    }

    /**
     * Display the performance by group report.
     */
    public function performanceByGroupReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.performance_by_group');
    }

    /**
     * Display the performance trends report.
     */
    public function performanceTrendsReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.performance_trends');
    }

    /**
     * Display the performance comparisons report.
     */
    public function performanceComparisonsReport(Request $request)
    {
        // Placeholder implementation
        return view('admin.reports.performance_comparisons');
    }
    
    /**
     * API endpoint to get groups by branch ID
     * Used for AJAX requests from the reports page
     */
    public function getGroupsByBranch($branchId)
    {
        // Check if user has access to this branch
        $user = auth()->user();
        $userBranches = $user->branches()->pluck('id')->toArray();
        
        if (!in_array($branchId, $userBranches) && !$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Get groups for the branch
        $groups = Group::where('branch_id', $branchId)->get(['id', 'name']);
        
        return response()->json($groups);
    }
}
