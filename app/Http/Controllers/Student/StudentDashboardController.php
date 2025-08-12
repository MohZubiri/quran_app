<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\ProgressLog;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isStudent()) {
                return redirect('/');
            }
            return $next($request);
        });
    }

    /**
     * Show the student dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        $student = $user->student()->with('group.branch')->first();

        if (!$student) {
            abort(404, 'Student not found');
        }

        // Get grades grouped by date
        $grades = Grade::where('student_id', $student->id)
            ->with(['subject', 'teacher'])
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date')
            ->map(function ($dayGrades) {
                return [
                    'date' => $dayGrades->first()->date->format('Y-m-d'),
                    'grades' => $dayGrades->groupBy('grade_type')->map(function ($gradeGroup) {
                        return $gradeGroup->first();
                    })
                ];
            });

        $recentGrades = new \Illuminate\Pagination\LengthAwarePaginator(
            $grades->values(),
            $grades->count(),
            10,
            request()->get('page', 1)
        );

        // Get recent attendance
        $recentAttendance = Attendance::where('student_id', $student->id)
            ->with(['group', 'teacher'])
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        return view('student.dashboard', compact('student', 'recentGrades', 'recentAttendance'));
    }

    /**
     * Show student's grades.
     */
    public function grades()
    {
        $user = Auth::user();
        $student = $user->student()->first();
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'لم يتم العثور على حساب الطالب');
        }
        
        $grades = $student->grades()->with(['subject', 'teacher'])
            ->orderBy('date', 'desc')
            ->paginate(15);

        // Calculate grade averages by type
        $gradeAverages = [];
        $gradeTypes = ['memorization', 'tajweed', 'recitation', 'behavior'];
        
        foreach ($gradeTypes as $type) {
            $average = $student->grades()->where('grade_type', $type)
                ->avg('grade') ?? 0;
            $gradeAverages[$type] = round($average, 1);
        }
        
        // Get overall average
        $overallAverage = $student->grades()->avg('grade') ?? 0;
        $overallAverage = round($overallAverage, 1);
        
        return view('student.grades', compact(
            'student', 
            'grades', 
            'gradeAverages', 
            'overallAverage'
        ));
    }

    /**
     * Show the student's attendance.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function attendance()
    {
        $user = Auth::user();
        $student = $user->student()->first();
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'لم يتم العثور على حساب الطالب');
        }
        
        $attendance = $student->attendance()->orderBy('date', 'desc')
            ->paginate(30);
            
        // Calculate attendance statistics
        $totalAttendance = $student->attendance()->count();
        $presentCount = $student->attendance()->where('status', 'present')->count();
        $absentCount = $student->attendance()->where('status', 'absent')->count();
        $lateCount = $student->attendance()->where('status', 'late')->count();
        $excusedCount = $student->attendance()->where('status', 'excused')->count();
        
        $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 0;
        
        // Get monthly attendance data for chart
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            
            $monthTotal = $student->attendance()->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->count();
                
            $monthPresent = $student->attendance()->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->where('status', 'present')
                ->count();
                
            $monthRate = $monthTotal > 0 ? round(($monthPresent / $monthTotal) * 100, 1) : 0;
            
            $monthlyData[] = [
                'month' => $month->format('M'),
                'rate' => $monthRate
            ];
        }
        
        return view('student.attendance', compact(
            'student', 
            'attendance', 
            'attendanceRate',
            'presentCount',
            'absentCount',
            'lateCount',
            'excusedCount',
            'totalAttendance',
            'monthlyData'
        ));
    }

    /**
     * Show student's progress logs.
     */
    public function progressLogs()
    {
        $user = Auth::user();
        $student = $user->student()->first();
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'لم يتم العثور على حساب الطالب');
        }
        
        $progressLogs = $student->progressLogs()->with('teacher')
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('student.progress', compact('student', 'progressLogs'));
    }

    /**
     * Show student's profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $student = $user->student()->with('group.branch')->first();
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'لم يتم العثور على حساب الطالب');
        }

        return view('student.profile', compact('student', 'user'));
    }
}
