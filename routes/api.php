<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Api\BranchController;
use App\Http\Controllers\Admin\Api\GroupController;

Route::prefix('admin/api')->name('admin.api.')->middleware(['auth'])->group(function () {
    // Branch related endpoints
    Route::get('branches/{branch}/groups', [BranchController::class, 'groups'])
        ->name('branches.groups');
    Route::get('branches/{branch}/teachers', [BranchController::class, 'teachers'])
        ->name('branches.teachers');

    // Group related endpoints
    Route::get('groups/{group}/students', [GroupController::class, 'students'])
        ->name('groups.students');
});
