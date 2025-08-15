<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subject;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Rules\UserBranchRule;

class SubjectController extends AdminController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view-subjects')->only(['index', 'show']);
        $this->middleware('permission:create-subjects')->only(['create', 'store']);
        $this->middleware('permission:edit-subjects')->only(['edit', 'update']);
        $this->middleware('permission:delete-subjects')->only('destroy');
    }

    /**
     * Display a listing of the subjects.
     */
    public function index()
    {
        
        $branches = Branch::all();
        $subjects = Subject::withCount(['groups'])->paginate(15);
        return view('admin.subjects.index', compact('subjects', 'branches'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        $branches = Branch::all();
        
        // التحقق من وجود فرع واحد فقط
        $defaultBranch = null;
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        }
        
        return view('admin.subjects.create', compact('branches', 'defaultBranch'));
    }

    /**
     * Store a newly created subject in storage.
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level' => 'required|string|max:50',
            'status' => 'required|in:active,inactive',
         
            'branch_id' => ['required', 'exists:branches,id', new UserBranchRule()],
        ]);

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم إضافة المادة بنجاح');
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        $subject->load(['groups']);
        return view('admin.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {   
        $branches = Branch::all();
        
        // التحقق من وجود فرع واحد فقط
        $defaultBranch = null;
        if ($branches->count() === 1) {
            $defaultBranch = $branches->first();
        }
        
        return view('admin.subjects.edit', compact('subject', 'branches', 'defaultBranch'));
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        // التحقق من وجود فرع واحد فقط
        $branches = Branch::all();
        if ($branches->count() === 1) {
            // إذا كان هناك فرع واحد فقط، نضيفه للطلب
            $request->merge(['branch_id' => $branches->first()->id]);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level' => 'required|string|max:50',
            'status' => 'required|in:active,inactive',
            'prerequisites' => 'nullable|string|max:500',
            'objectives' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'branch_id' => ['required', 'exists:branches,id', new UserBranchRule()],
        ]);

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم تحديث المادة بنجاح');
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy(Subject $subject)
    {
        // Check if subject has any groups
        if ($subject->groups()->count() > 0) {
            return redirect()->route('admin.subjects.index')
                ->with('error', 'لا يمكن حذف المادة لوجود مجموعات مرتبطة بها');
        }

        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم حذف المادة بنجاح');
    }
}
