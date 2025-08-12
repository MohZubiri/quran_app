<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Constructor to apply auth middleware to all admin controllers
     */
    public function __construct()
    {
        // Apply auth middleware to all admin controllers
        $this->middleware('auth');
        
        // We'll apply the permission middleware in the routes instead of here
    }

    /**
     * Get the dashboard view
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Get the branches that belong to the authenticated user
     */
    protected function getUserBranches()
    {
        return Branch::where('user_id', Auth::id())->get();
    }

    /**
     * Check if a branch belongs to the authenticated user
     */
    protected function branchBelongsToUser($branchId)
    {
        return Branch::where('id', $branchId)
            ->where('user_id', Auth::id())
            ->exists();
    }
}
