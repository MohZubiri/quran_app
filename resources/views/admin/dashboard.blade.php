@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>لوحة تحكم نظام تعليم القرآن</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="p-4">
                        <h2>مرحباً بك في نظام إدارة حلقات تعليم القرآن</h2>
                        <p class="lead">إدارة حلقات تعليم القرآن والطلاب والمعلمين والمناهج في مكان واحد.</p>
                        
                        <div class="row mt-5">
                            @if(auth()->user()->getRoleNames()[0] === 'super_admin')
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    الفروع</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Branch::count() }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-building fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(auth()->user()->getRoleNames()[0] !== 'teacher')
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    الطلاب</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @if(auth()->user()->getRoleNames()[0] === 'super_admin')
                                                        {{ \App\Models\Student::count() }}
                                                    @else
                                                        {{ \App\Models\Student::join('groups', 'students.group_id', '=', 'groups.id')
                                                            ->join('branches', 'groups.branch_id', '=', 'branches.id')
                                                            ->join('users', 'users.branch_id', '=', 'branches.id')
                                                            ->where('users.id', auth()->id())
                                                            ->count('students.id') }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    المعلمين</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @if(auth()->user()->getRoleNames()[0] === 'super_admin')
                                                        {{ \App\Models\Teacher::count() }}
                                                    @else
                                                        {{ \App\Models\Teacher::join('branches', 'teachers.branch_id', '=', 'branches.id')
                                                            ->join('users', 'users.branch_id', '=', 'branches.id')
                                                            ->where('users.id', auth()->id())
                                                            ->count('teachers.id') }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    الحلقات</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    @if(auth()->user()->getRoleNames()[0] === 'super_admin')
                                                        {{ \App\Models\Group::count() }}
                                                    @elseif(auth()->user()->getRoleNames()[0] === 'teacher')
                                                        {{ \App\Models\Group::where('teacher_id', auth()->id())->count() }}
                                                    @else
                                                    
                                                    {{ \App\Models\Group::where('branch_id', auth()->user()->branch_id)->count() }}
                                                      
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-users fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">الإجراءات السريعة</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @if(auth()->user()->getRoleNames()[0] === 'super_admin')
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.branches.index') }}" class="btn btn-primary btn-block">
                                                    <i class="fas fa-building me-2"></i> إدارة الفروع
                                                </a>
                                            </div>
                                            @endif

                                            @if(auth()->user()->getRoleNames()[0] !== 'teacher')
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.students.index') }}" class="btn btn-success btn-block">
                                                    <i class="fas fa-user-graduate me-2"></i> إدارة الطلاب
                                                </a>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.teachers.index') }}" class="btn btn-info btn-block">
                                                    <i class="fas fa-chalkboard-teacher me-2"></i> إدارة المعلمين
                                                </a>
                                            </div>
                                            @endif

                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.groups.index') }}" class="btn btn-warning btn-block">
                                                    <i class="fas fa-users me-2"></i> 
                                                    @if(auth()->user()->getRoleNames()[0] === 'teacher')
                                                        حلقاتي
                                                    @else
                                                        إدارة الحلقات
                                                    @endif
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @if(auth()->user()->getRoleNames()[0] !== 'teacher')
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary btn-block">
                                                    <i class="fas fa-book me-2"></i> إدارة المواد
                                                </a>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.enrollments.index') }}" class="btn btn-dark btn-block">
                                                    <i class="fas fa-user-plus me-2"></i> إدارة التسجيلات
                                                </a>
                                            </div>
                                            @endif

                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.attendance.index') }}" class="btn btn-primary btn-block">
                                                    <i class="fas fa-clipboard-check me-2"></i> 
                                                    @if(auth()->user()->getRoleNames()[0] === 'teacher')
                                                        تسجيل الحضور
                                                    @else
                                                        إدارة الحضور
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('admin.grades.index') }}" class="btn btn-success btn-block">
                                                    <i class="fas fa-chart-line me-2"></i> 
                                                    @if(auth()->user()->getRoleNames()[0] === 'teacher')
                                                        تسجيل الدرجات
                                                    @else
                                                        إدارة الدرجات
                                                    @endif
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
