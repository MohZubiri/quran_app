@extends('layouts.admin')

@section('title', 'تفاصيل المعلم')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.teachers.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى قائمة المعلمين
    </a>
    <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit me-1"></i> تعديل بيانات المعلم
    </a>
    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
        <i class="fas fa-trash me-1"></i> حذف المعلم
    </button>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف المعلم "{{ $teacher->name }} "؟ هذا الإجراء لا يمكن التراجع عنه.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">معلومات المعلم</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 30%">الاسم الكامل</th>
                            <td>{{ $teacher->name }}</td>
                        </tr>
                        <tr>
                            <th>البريد الإلكتروني</th>
                            <td>{{ $teacher->email }}</td>
                        </tr>
                        <tr>
                            <th>رقم الهاتف</th>
                            <td>{{ $teacher->phone ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>المؤهل العلمي</th>
                            <td>{{ $teacher->qualification ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>التخصص</th>
                            <td>{{ $teacher->specialization ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ التعيين</th>
                            <td>{{ $teacher->hire_date ? $teacher->hire_date->format('Y-m-d') : 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                @if($teacher->status == 'active')
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>الفرع</th>
                            <td>{{ $teacher->branch->name ?? 'غير محدد' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($teacher->bio)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">نبذة عن المعلم</h5>
            </div>
            <div class="card-body">
                <p>{{ $teacher->bio }}</p>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">إحصائيات المعلم</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">الحلقات</h5>
                                <p class="card-text display-6">{{ $teacher->groups->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">الطلاب</h5>
                                <p class="card-text display-6">{{ $teacher->students()->count() }}</p>
                            </div>
                        </div>
                    </div>
                   
                    <div class="col-md-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">التقييمات</h5>
                                <p class="card-text display-6">{{ $teacher->grades->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">الحلقات التي يدرسها المعلم</h5>
                <a href="{{ route('admin.groups.create', ['teacher_id' => $teacher->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> إضافة حلقة جديدة
                </a>
            </div>
            <div class="card-body">
                @if($teacher->groups->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الحلقة</th>
                                <th>المادة</th>
                                <th>الجدول</th>
                                <th>عدد الطلاب</th>
                                <th>الفرع</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacher->groups as $group)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $group->name ?? 'حلقة ' . $group->id }}</td>
                                <td>{{ $group->subject->name ?? 'غير محدد' }}</td>
                                <td>{{ $group->schedule ?? 'غير محدد' }}</td>
                                <td>{{ $group->enrollments->count() }} / {{ $group->max_students }}</td>
                                <td>{{ $group->branch->name ?? 'غير محدد' }}</td>
                                <td>
                                    <a href="{{ route('admin.groups.show', $group->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    لا توجد حلقات مسندة لهذا المعلم حتى الآن.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">سجل الحضور الأخير</h5>
                <a href="{{ route('admin.attendance.create', ['teacher_id' => $teacher->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> تسجيل حضور جديد
                </a>
            </div>
            <div class="card-body">
                @if($teacher->attendances()->latest()->take(10)->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>الحلقة</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacher->attendances()->latest()->take(10)->get() as $attendance)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $attendance->date->format('Y-m-d') }}</td>
                                <td>{{ $attendance->group->name ?? 'حلقة ' . $attendance->group_id }}</td>
                                <td>
                                    @if($attendance->status == 'present')
                                        <span class="badge bg-success">حاضر</span>
                                    @elseif($attendance->status == 'absent')
                                        <span class="badge bg-danger">غائب</span>
                                    @elseif($attendance->status == 'late')
                                        <span class="badge bg-warning">متأخر</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $attendance->notes ?? 'لا توجد ملاحظات' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('admin.attendance.index', ['teacher_id' => $teacher->id]) }}" class="btn btn-sm btn-outline-primary">
                        عرض سجل الحضور الكامل
                    </a>
                </div>
                @else
                <div class="alert alert-info">
                    لا يوجد سجل حضور لهذا المعلم حتى الآن.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
