<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProgressLogController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GradesController;
use App\Http\Controllers\Admin\UserPermissionController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Admin\StudyPlanController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth', 'permission:access-admin-panel'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Branches
        Route::resource('branches', BranchController::class);

        // Students
        Route::resource('students', StudentController::class);
        Route::get('students/{student}/progress', [StudentController::class, 'progress'])->name('students.progress');
        Route::post('students/{student}/create-account', [StudentController::class, 'createAccount'])->name('students.create-account');

        // Teachers
        Route::resource('teachers', TeacherController::class);
        Route::post('teachers/{teacher}/assign-user', [TeacherController::class, 'assignUser'])->name('teachers.assign-user');

        // Subjects
        Route::resource('subjects', SubjectController::class);

        // Groups
        Route::resource('groups', GroupController::class);
        Route::get('groups/{group}/students', [GroupController::class, 'students'])->name('groups.students');
        Route::get('groups/{group}/attendance', [GroupController::class, 'attendance'])->name('groups.attendance');
        Route::get('groups/{group}/grades', [GroupController::class, 'grades'])->name('groups.grades');

        // Branch related routes
        Route::get('branches/{branch}/groups', [BranchController::class, 'groups'])->name('branches.groups');
        Route::get('branches/{branch}/teachers', [BranchController::class, 'teachers'])->name('branches.teachers');

        // Enrollments
        Route::resource('enrollments', EnrollmentController::class);
        Route::resource('study_plans', StudyPlanController::class);

        // User Management
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);

        // User Roles & Permissions
        Route::get('users/{user}/roles', [UserController::class, 'roles'])->name('users.roles');
        Route::post('users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');
        Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
        Route::post('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');

        // Attendance
        Route::resource('attendance', AttendanceController::class);
        Route::post('attendance/bulk', [AttendanceController::class, 'storeBulk'])->name('attendance.bulk');

        // Grades
        Route::resource('grades', GradesController::class);
        Route::post('grades/bulk', [GradeController::class, 'storeBulk'])->name('grades.bulk');

        // Progress Logs
        Route::resource('progress-logs', ProgressLogController::class);

        // User Permissions Management
        Route::middleware(['permission:manage-user-permissions'])->group(function () {
            Route::get('users/{user}/permissions', [UserPermissionController::class, 'edit'])->name('users.permissions');
            Route::put('users/{user}/roles', [UserPermissionController::class, 'updateRoles'])->name('users.update-roles');
            Route::put('users/{user}/permissions', [UserPermissionController::class, 'updatePermissions'])->name('users.update-permissions');

            // Role-user assignment routes
            Route::post('roles/{role}/assign-users', [RoleController::class, 'assignUsers'])->name('roles.assign-users');
            Route::delete('roles/{role}/users/{user}', [RoleController::class, 'removeUser'])->name('roles.remove-user');
        });
    });
});

// Student Routes
Route::middleware(['auth', 'permission:access-student-panel'])->prefix('student')->name('student.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Student\StudentDashboardController::class, 'index'])->name('dashboard');

    // Grades
    Route::get('/grades', [\App\Http\Controllers\Student\StudentDashboardController::class, 'grades'])->name('grades');

    // Attendance
    Route::get('/attendance', [\App\Http\Controllers\Student\StudentDashboardController::class, 'attendance'])->name('attendance');

    // Progress Logs
    Route::get('/progress', [\App\Http\Controllers\Student\StudentDashboardController::class, 'progressLogs'])->name('progress');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Student\StudentDashboardController::class, 'profile'])->name('profile');
});

// Redirect authenticated users to appropriate dashboard
Route::middleware(['auth'])->get('/home', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif (auth()->user()->isTeacher()) {
        return redirect()->route('admin.dashboard'); // Teachers use admin dashboard for now
    } elseif (auth()->user()->isStudent()) {
        return redirect()->route('student.dashboard');
    }

    return redirect()->route('login');
})->name('home');

require __DIR__.'/auth.php';
