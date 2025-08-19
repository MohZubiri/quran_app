<?php

namespace App\Http\Controllers\Admin;

use App\Models\StudentPlan;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudentPlanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'saving_from' => 'required|string',
            'saving_to' => 'required|string',
            'review_from' => 'required|string',
            'review_to' => 'required|string',
            'month' => 'required|string',
            'group_id' => 'required|exists:groups,id',
        ]);

        $plan = StudentPlan::create($validated);

        return redirect()->back()->with('success', 'تم إنشاء خطة الطالب بنجاح');
    }

    public function show(StudentPlan $studentPlan)
    {
        return response()->json($studentPlan->load('student'));
    }

    public function update(Request $request, StudentPlan $studentPlan)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'saving_from' => 'required|string',
            'saving_to' => 'required|string',
            'review_from' => 'required|string',
            'review_to' => 'required|string',
            'month' => 'required|string',
        ]);

        $studentPlan->update($validated);

        return redirect()->back()->with('success', 'تم تحديث خطة الطالب بنجاح');
    }

    public function destroy(StudentPlan $studentPlan)
    {
        $studentPlan->delete();
        return redirect()->back()->with('success', 'تم حذف خطة الطالب بنجاح');
    }
}
