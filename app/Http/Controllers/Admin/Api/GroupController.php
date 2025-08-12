<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends AdminController
{
    /**
     * Get all students for a specific group
     */
    public function students(Group $group)
    {
       
        $this->authorize('view', $group);
        
        return response()->json($group->students);
    }
}
