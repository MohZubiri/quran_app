@extends('layouts.admin')

@section('title', 'سجل الحضور')

@section('actions')
<div class="btn-group" role="group">
    @can('create-attendance')
    <a href="{{ route('admin.attendance.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus-circle me-1"></i> تسجيل حضور جديد
    </a>
    @endcan
    @if(request()->filled('group_id'))
    <a href="{{ route('admin.groups.show', request()->group_id) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى الحلقة
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">سجل الحضور</h5>
        @can('create-attendance')
        <a href="{{ route('admin.attendance.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> تسجيل حضور جديد
        </a>
        @endcan
    </div>
    <div class="card-body">
        <form action="{{ route('admin.attendance.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="branch_id" class="form-label">الفرع</label>
                    <select class="form-select" id="branch_id" name="branch_id" required>
                        <option value="">جميع الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="group_id" class="form-label">الحلقة</label>
                    <select class="form-select" id="group_id" name="group_id">
                        <option value="">جميع الحلقات</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="student_id" class="form-label">الطالب</label>
                    <select class="form-select" id="student_id" name="student_id">
                        <option value="">جميع الطلاب</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">جميع الحالات</option>
                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>حاضر</option>
                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>غائب</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>متأخر</option>
                        <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>معذور</option>
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
                
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>

        @if($attendance->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>الطالب</th>
                        <th>الحلقة</th>
                        <th>المعلم</th>
                        <th>الحالة</th>
                        <th>ملاحظات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendance as $record)
                    <tr>
                        <td>{{ $loop->iteration + ($attendance->perPage() * ($attendance->currentPage() - 1)) }}</td>
                        <td>{{ $record->date->format('Y-m-d') }}</td>
                        <td>
                            @can('view-students')
                            <a href="{{ route('admin.students.show', $record->student_id) }}">
                                {{ $record->student->name }} 
                            </a>
                            @else
                                {{ $record->student->name }}
                            @endcan
                        </td>
                        <td>
                            @can('view-groups')
                            <a href="{{ route('admin.groups.show', $record->group_id) }}">
                                {{ $record->group->name }}
                            </a>
                            @else
                                {{ $record->group->name }}
                            @endcan
                        </td>
                        <td>
                            @can('view-teachers')
                            <a href="{{ route('admin.teachers.show', $record->teacher_id) }}">
                                {{ $record->teacher->name }}
                            </a>
                            @else
                                {{ $record->teacher->name }}
                            @endcan
                        </td>
                        <td>
                            @if($record->status == 'present')
                                <span class="badge bg-success">حاضر</span>
                            @elseif($record->status == 'absent')
                                <span class="badge bg-danger">غائب</span>
                            @elseif($record->status == 'late')
                                <span class="badge bg-warning">متأخر</span>
                            @elseif($record->status == 'excused')
                                <span class="badge bg-info">معذور</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($record->notes, 30) }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('edit-attendance')
                                <a href="{{ route('admin.attendance.edit', $record->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete-attendance')
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $record->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endcan
                            </div>
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $record->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">تأكيد الحذف</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                        </div>
                                        <div class="modal-body">
                                            هل أنت متأكد من رغبتك في حذف سجل حضور الطالب "{{ $record->student->name }} " بتاريخ {{ $record->date->format('Y-m-d') }}؟
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <form action="{{ route('admin.attendance.destroy', $record->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">حذف</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $attendance->links() }}
        </div>
        
        @else
        <div class="alert alert-info">
            لا يوجد سجلات حضور حتى الآن
        </div>
        @endif
    </div>
</div>

<!-- Attendance Statistics -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">إحصائيات الحضور</h5>
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
                                <td><span class="badge bg-success">حاضر</span></td>
                                <td>{{ $stats['by_status']['present']['count'] }}</td>
                                <td>{{ $stats['by_status']['present']['percentage'] }}%</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">غائب</span></td>
                                <td>{{ $stats['by_status']['absent']['count'] }}</td>
                                <td>{{ $stats['by_status']['absent']['percentage'] }}%</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning">متأخر</span></td>
                                <td>{{ $stats['by_status']['late']['count'] }}</td>
                                <td>{{ $stats['by_status']['late']['percentage'] }}%</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">معذور</span></td>
                                <td>{{ $stats['by_status']['excused']['count'] }}</td>
                                <td>{{ $stats['by_status']['excused']['percentage'] }}%</td>
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
@endsection

@section('scripts')
<script>
    // تحديث قوائم الحلقات والطلاب عند تغيير الفرع
    document.getElementById('branch_id').addEventListener('change', function() {
        const branchId = this.value;
        
        // يمكن إضافة كود Ajax هنا لتحديث قائمة الحلقات والطلاب بناءً على الفرع المختار
    });
    
    // تحديث قائمة الطلاب عند تغيير الحلقة
    document.getElementById('group_id').addEventListener('change', function() {
        const groupId = this.value;
        
        // يمكن إضافة كود Ajax هنا لتحديث قائمة الطلاب بناءً على الحلقة المختارة
    });
</script>
@endsection
