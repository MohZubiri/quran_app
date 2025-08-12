@extends('layouts.admin')

@section('title', 'سجلات التقدم')

@section('actions')
<a href="{{ route('admin.progress-logs.create') }}" class="btn btn-primary btn-sm">
    <i class="fas fa-plus me-1"></i> إضافة سجل تقدم جديد
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">سجلات التقدم</h5>
    </div>
    
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('admin.progress-logs.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="branch_id" class="form-label">الفرع</label>
                    <select class="form-select" id="branch_id" name="branch_id">
                        <option value="">-- جميع الفروع --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="teacher_id" class="form-label">المعلم</label>
                    <select class="form-select" id="teacher_id" name="teacher_id">
                        <option value="">-- جميع المعلمين --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="group_id" class="form-label">المجموعة</label>
                    <select class="form-select" id="group_id" name="group_id">
                        <option value="">-- جميع المجموعات --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="student_id" class="form-label">الطالب</label>
                    <select class="form-select" id="student_id" name="student_id">
                        <option value="">-- جميع الطلاب --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="date_from" class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-3">
                    <label for="date_to" class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> تصفية
                    </button>
                    <a href="{{ route('admin.progress-logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء التصفية
                    </a>
                </div>
            </div>
        </form>

        <!-- Results Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الطالب</th>
                        <th>المجموعة</th>
                        <th>المعلم</th>
                        <th>الفرع</th>
                        <th>التاريخ</th>
                        <th>الحفظ</th>
                        <th>المراجعة</th>
                        <th>التلاوة</th>
                        <th>التجويد</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($progressLogs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $log->student->name }}</td>
                            <td>{{ $log->student->group->name }}</td>
                            <td>{{ $log->student->group->teacher->name }}</td>
                            <td>{{ $log->student->group->branch->name }}</td>
                            <td>{{ $log->date->format('Y-m-d') }}</td>
                            <td>{{ $log->memorization }}</td>
                            <td>{{ $log->revision }}</td>
                            <td>{{ $log->recitation }}</td>
                            <td>{{ $log->tajweed }}</td>
                            <td>
                                <a href="{{ route('admin.progress-logs.show', $log) }}" class="btn btn-sm btn-info" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.progress-logs.edit', $log) }}" class="btn btn-sm btn-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.progress-logs.destroy', $log) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا السجل؟')" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">لا توجد سجلات تقدم</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $progressLogs->links() }}
        </div>
    </div>
</div>
@endsection
