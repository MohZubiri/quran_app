<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends AdminController
{
    /**
     * Get all groups for a specific branch
     */
    public function groups(Branch $branch)
    {
        $this->authorize('view', $branch);
        
        return response()->json($branch->groups);
    }

    /**
     * Get all teachers for a specific branch
     */
    public function teachers(Branch $branch)
    {
        $this->authorize('view', $branch);
        
        return response()->json($branch->teachers);
    }
}
