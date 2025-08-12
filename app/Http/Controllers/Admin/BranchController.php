<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BranchController extends AdminController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:view-branches')->only(['index', 'show']);
        $this->middleware('permission:create-branches')->only(['create', 'store']);
        $this->middleware('permission:edit-branches')->only(['edit', 'update']);
        $this->middleware('permission:delete-branches')->only('destroy');
    }

    /**
     * Display a listing of the branches.
     */
    public function index()
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $branches = Branch::withCount(['students', 'teachers', 'groups'])->paginate(15);
        return view('admin.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        return view('admin.branches.create');
    }

    /**
     * Store a newly created branch in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'required|string|max:255',
            'manager_phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Add the authenticated user's ID
        $validated['user_id'] = Auth::id();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = time() . '_' . $logo->getClientOriginalName();
            
            // Make sure the directory exists
            Storage::makeDirectory('public/branch_logos');
            
            // Store the file
            try {
                if ($logo->storeAs('public/branch_logos', $filename)) {
                    $validated['logo'] = 'branch_logos/' . $filename;
                } else {
                    return back()->with('error', 'فشل في رفع الصورة. الرجاء المحاولة مرة أخرى.');
                }
            } catch (\Exception $e) {
                Log::error('Logo upload failed: ' . $e->getMessage());
                return back()->with('error', 'حدث خطأ أثناء رفع الصورة. الرجاء المحاولة مرة أخرى.');
            }
        }

        Branch::create($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified branch.
     */
    public function show(Branch $branch)
    {
        // التصفية تتم تلقائياً عبر Global Scope
        $branch->load(['students', 'teachers', 'groups']);
        return view('admin.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'required|string|max:255',
            'manager_phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($branch->logo) {
                Storage::delete('public/' . $branch->logo);
            }

            $logo = $request->file('logo');
            $filename = $logo->getClientOriginalName();
           
            // Make sure the directory exists
           // Storage::makeDirectory('public/branch_logos');
            
            // Store the file
            try {
                if (Storage::putFileAs('public/images', $logo, $filename)) {
                    $validated['logo'] = 'images/' . $filename;
                } else {
                    return back()->with('error', 'فشل في رفع الصورة. الرجاء المحاولة مرة أخرى.');
                }
            } catch (\Exception $e) {
                dd($e);
                Log::error('Logo upload failed: ' . $e->getMessage());
                return back()->with('error', 'حدث خطأ أثناء رفع الصورة. الرجاء المحاولة مرة أخرى.');
            }
        }

        $branch->update($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified branch from storage.
     */
    public function destroy(Branch $branch)
    {
        // Check if branch has any related records
        if ($branch->students()->count() > 0 || 
            $branch->teachers()->count() > 0 || 
            $branch->groups()->count() > 0) {
            return back()->with('error', 'Cannot delete branch with related records.');
        }

        $branch->delete();

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch deleted successfully.');
    }

    /**
     * Get all groups for a specific branch
     */
    public function groups(Branch $branch)
    {
        $this->authorize('view', $branch);
        
        $groups = $branch->groups()->select('id', 'name')->get();
        return response()->json(['groups' => $groups]);
    }

    /**
     * Get all teachers for a specific branch
     */
    public function teachers(Branch $branch)
    {
        $this->authorize('view', $branch);
        
        $teachers = $branch->teachers()->select('id', 'name',)->get();
        return response()->json(['teachers' => $teachers]);
    }
}
