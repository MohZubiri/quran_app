@extends('layouts.admin')

@section('title', 'تفاصيل الطالب')

@section('styles')
<!-- Add any additional styles here -->
@endsection

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى قائمة الطلاب
    </a>
    <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit me-1"></i> تعديل بيانات الطالب
    </a>
    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
        <i class="fas fa-trash me-1"></i> حذف الطالب
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
                هل أنت متأكد من رغبتك في حذف الطالب "{{ $student->name }}"؟ هذا الإجراء لا يمكن التراجع عنه.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="d-inline">
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
    <div class="col-md-4">
        <div class="mb-4 card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 card-title">معلومات الطالب</h5>
                    @if(!$student->user_id)
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal">
                            <i class="fas fa-user-plus me-1"></i>
                            إنشاء حساب
                        </button>
                    @else
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>
                            تم إنشاء الحساب
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 40%">الاسم الكامل</th>
                            <td>{{ $student->name }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ الميلاد</th>
                            <td>{{ $student->birth_date ? $student->birth_date->format('Y-m-d') : 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>العمر</th>
                            <td>{{ $student->birth_date ? $student->birth_date->age . ' سنة' : 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>الجنس</th>
                            <td>{{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}</td>
                        </tr>
                        <tr>
                            <th>رقم الهاتف</th>
                            <td>{{ $student->phone ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>البريد الإلكتروني</th>
                            <td>{{ $student->email ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>العنوان</th>
                            <td>{{ $student->address ?? 'غير محدد' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
    <div class="mb-4 card">
            <div class="card-header">
                <h5 class="card-title">إحصائيات الطالب</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-6">
                        <div class="text-white card bg-primary">
                            <div class="text-center card-body">
                                <h6 class="card-title">الحلقات</h6>
                                <p class="card-text display-6">{{ $student->enrollments->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                        <div class="text-white card bg-success">
                            <div class="text-center card-body">
                                <h6 class="card-title">نسبة الحضور</h6>
                                <p class="card-text display-6">
                                    @php
                                        $attendanceCount = $student->attendance->count();
                                        $presentCount = $student->attendance->where('status', 'present')->count();
                                        $attendanceRate = $attendanceCount > 0 ? round(($presentCount / $attendanceCount) * 100) : 0;
                                    @endphp
                                    {{ $attendanceRate }}%
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                        <div class="text-white card bg-info">
                            <div class="text-center card-body">
                                <h6 class="card-title">التقييمات</h6>
                                <p class="card-text display-6">{{ $student->grades->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                        <div class="text-white card bg-warning">
                            <div class="text-center card-body">
                                <h6 class="card-title">متوسط الدرجات</h6>
                                <p class="card-text display-6">
                                    @php
                                        $grades = $student->grades;
                                        $avgScore = $grades->count() > 0 ? round($grades->avg('grade')) : 0;
                                    @endphp
                                    {{ $avgScore }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-4 card">
            <div class="card-header">
                <h5 class="card-title">معلومات التسجيل</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 40%">الفرع</th>
                            <td>{{ $student->branch->name ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ التسجيل</th>
                            <td>{{ $student->created_at ? $student->created_at->format('Y-m-d') : 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>المستوى الحالي</th>
                            <td>{{ $student->current_level ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                @if($student->status == 'active')
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mb-4 card">
            <div class="card-header">
                <h5 class="card-title">حساب الطالب </h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 40%">الايميل</th>
                            <td>{{ $account->email??'_____' }}</td>
                        </tr>
                        <tr>
                            <th>كلمة السر  </th>
                            <td><input type="password" value="{{ $account->password??'_____' }}" disabled> </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>

@if($student->initial_assessment)
<div class="row">
    <div class="col-12">
        <div class="mb-4 card">
            <div class="card-header">
                <h5 class="card-title">التقييم الأولي</h5>
            </div>
            <div class="card-body">
                <p>{{ $student->initial_assessment }}</p>
            </div>
        </div>
    </div>
</div>
@endif

@if($student->notes)
<div class="row">
    <div class="col-12">
        <div class="mb-4 card">
            <div class="card-header">
                <h5 class="card-title">ملاحظات إضافية</h5>
            </div>
            <div class="card-body">
                <p>{{ $student->notes }}</p>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">الحلقات المسجل فيها الطالب</h5>
                <a href="{{ route('admin.enrollments.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> تسجيل في حلقة جديدة
                </a>
            </div>
            <div class="card-body">
                @if($student->enrollments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الحلقة</th>
                                <th>المادة</th>
                                <th>المعلم</th>
                                <th>الجدول</th>
                                <th>تاريخ التسجيل</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->enrollments as $enrollment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $enrollment->group->name ?? 'حلقة ' . $enrollment->group_id }}</td>
                                <td>{{ $enrollment->group->subject->name ?? 'غير محدد' }}</td>
                                <td>{{ $enrollment->group->teacher->name ?? '' }} </td>
                                <td>{{ $enrollment->group->schedule ?? 'غير محدد' }}</td>
                                <td>{{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('Y-m-d') : 'غير محدد' }}</td>
                                <td>
                                    @if($enrollment->status == 'active')
                                        <span class="badge bg-success">نشط</span>
                                    @elseif($enrollment->status == 'completed')
                                        <span class="badge bg-info">مكتمل</span>
                                    @elseif($enrollment->status == 'dropped')
                                        <span class="badge bg-danger">منسحب</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $enrollment->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.groups.show', $enrollment->group_id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.enrollments.edit', $enrollment->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    الطالب غير مسجل في أي حلقات حتى الآن.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-4 row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">سجل الحضور الأخير</h5>
                <a href="{{ route('admin.attendance.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> تسجيل حضور جديد
                </a>
            </div>
            <div class="card-body">
                @if($student->attendance()->latest()->take(5)->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>الحلقة</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->attendance()->latest()->take(5)->get() as $attendance)
                            <tr>
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
                <div class="mt-3 d-flex justify-content-end">
                    <a href="{{ route('admin.attendance.index', ['student_id' => $student->id]) }}" class="btn btn-sm btn-outline-primary">
                        عرض سجل الحضور الكامل
                    </a>
                </div>
                @else
                <div class="alert alert-info">
                    لا يوجد سجل حضور لهذا الطالب حتى الآن.
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">name</h5>
                <a href="{{ route('admin.grades.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> إضافة تقييم جديد
                </a>
            </div>
            <div class="card-body">
                @if($student->grades()->latest()->take(5)->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>المادة</th>
                                <th>المعلم</th>
                                <th>الدرجة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->grades()->latest()->take(4)->get() as $grade)
                            <tr>
                                <td>{{ $grade->date->format('Y-m-d') }}</td>
                                <td>{{ $grade->subject->name ?? 'غير محدد' }}</td>
                                <td> {{ $grade->teacher->name ?? 'غير محدد' }}</td>
                                <td>
                                    <span class="badge {{ $grade->grade >= 90 ? 'bg-success' : ($grade->grade >= 70 ? 'bg-info' : ($grade->grade >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                        {{ $grade->grade }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($grade->notes, 30) ?? 'لا توجد ملاحظات' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    <a href="{{ route('admin.grades.index', ['student_id' => $student->id]) }}" class="btn btn-sm btn-outline-primary">
                        عرض سجل التقييمات الكامل
                    </a>
                </div>
                @else
                <div class="alert alert-info">
                    لا توجد تقييمات لهذا الطالب حتى الآن.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>


<!-- Create Account Modal -->
<div class="modal fade" id="createAccountModal" tabindex="-1" aria-labelledby="createAccountModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.students.create-account', $student->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createAccountModalLabel">إنشاء حساب للطالب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إنشاء الحساب</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all modals
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        new bootstrap.Modal(modal);
    });
});
</script>
@endsection
