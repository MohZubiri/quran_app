<?php

namespace App\Http\Controllers\Admin;

use App\Models\StudyPlan;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class StudyPlanController extends Controller
{
    public function index()
    {
        $plans = StudyPlan::all();
        return view('admin.study_plans.index', compact('plans'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('admin.study_plans.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_number'    => 'required|unique:study_plans,plan_number',
            'group_number'   => 'required|exists:groups,id',
            'lessons_count'  => 'required|integer|min:0',
            'min_performance'=> 'required|numeric|min:0|max:100',
            'status'         => 'required|in:0,1',
        ]);

        $plan = StudyPlan::create($validated);

        // عند ربط الخطة بالمجموعة تحديث الطلاب
        Student::where('group_id', $plan->group_number)
            ->update(['plan_number' => $plan->plan_number]);

        return redirect()->route('admin.study_plans.index')->with('success', 'تم إنشاء الخطة وربطها بالمجموعة بنجاح');
    }

    public function edit(StudyPlan $studyPlan)
    {
        $groups = Group::all();
        return view('admin.study_plans.edit', compact('studyPlan', 'groups'));
    }

    public function update(Request $request, StudyPlan $studyPlan)
    {
        $validated = $request->validate([
            'plan_number'    => 'required|unique:study_plans,plan_number,' . $studyPlan->id,
            'group_number'   => 'required|exists:groups,id',
            'lessons_count'  => 'required|integer|min:0',
            'min_performance'=> 'required|numeric|min:0|max:100',
            'status'         => 'required|in:0,1',
        ]);

        $studyPlan->update($validated);

        // تحديث الطلاب عند تعديل ربط الخطة بالمجموعة
        Student::where('group_id', $studyPlan->group_number)
            ->update(['plan_number' => $studyPlan->plan_number]);

        return redirect()->route('admin.study_plans.index')->with('success', 'تم تحديث الخطة وربطها بالمجموعة بنجاح');
    }
    public function show(StudyPlan $study_plan)
    {
        $study_plan->load('group');
        return view('admin.study_plans.show', compact('study_plan'));
    }
    public function destroy(StudyPlan $studyPlan)
    {
        $studyPlan->delete();
        return redirect()->route('admin.study_plans.index')->with('success', 'تم حذف الخطة بنجاح');
    }
}
