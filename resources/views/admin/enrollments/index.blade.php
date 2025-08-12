@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4">إدارة التسجيلات</h2>
            @can('create-enrollments')
            <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> إضافة تسجيل جديد
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.enrollments.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="branch_id" class="form-label">الفرع</label>
                    <select class="form-select" id="branch_id" name="branch_id">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="group_id" class="form-label">المجموعة</label>
                    <select class="form-select" id="group_id" name="group_id">
                        <option value="">كل المجموعات</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-redo"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Enrollments List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة التسجيلات</h5>
        </div>
        <div class="card-body">
            @if($enrollments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الطالب</th>
                                <th>المجموعة</th>
                                <th>الفرع</th>
                                <th>تاريخ التسجيل</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $enrollment)
                                <tr>
                                    <td>{{ $loop->iteration + ($enrollments->perPage() * ($enrollments->currentPage() - 1)) }}</td>
                                    <td>
                                        @can('view-students')
                                        <a href="{{ route('admin.students.show', $enrollment->student_id) }}">
                                            {{ $enrollment->student->name }} 
                                        </a>
                                        @else
                                            {{ $enrollment->student->name }} 
                                        @endcan
                                    </td>
                                    <td>
                                        @can('view-groups')
                                        <a href="{{ route('admin.groups.show', $enrollment->group_id) }}">
                                            {{ $enrollment->group->name }}
                                        </a>
                                        @else
                                            {{ $enrollment->group->name }}
                                        @endcan
                                    </td>
                                    <td>{{ $enrollment->group->branch->name }}</td>
                                    <td>{{ $enrollment->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if($enrollment->status == 'active')
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                           
                                            
                                            @can('edit-enrollments')
                                            <a href="{{ route('admin.enrollments.edit', $enrollment->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('delete-enrollments')
                                            <form action="{{ route('admin.enrollments.destroy', $enrollment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التسجيل؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $enrollments->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    لا يوجد تسجيلات حتى الآن
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">إحصائيات التسجيل</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>الحالة</th>
                                    <th>العدد</th>
                                    <th>النسبة المئوية</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-success">نشط</span></td>
                                    <td>{{ $stats['by_status']['active']['count'] }}</td>
                                    <td>{{ $stats['by_status']['active']['percentage'] }}%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">غير نشط</span></td>
                                    <td>{{ $stats['by_status']['inactive']['count'] }}</td>
                                    <td>{{ $stats['by_status']['inactive']['percentage'] }}%</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <td>المجموع</td>
                                    <td>{{ $stats['total'] }}</td>
                                    <td>100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
