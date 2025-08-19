@extends('layouts.admin')

@section('title', 'تفاصيل الحلقة')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.groups.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى قائمة الحلقات
    </a>
    <a href="{{ route('admin.groups.edit', $group->id) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit me-1"></i> تعديل الحلقة
    </a>
    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
        <i class="fas fa-trash me-1"></i> حذف الحلقة
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
                هل أنت متأكد من رغبتك في حذف الحلقة "{{ $group->name }}"؟ هذا الإجراء لا يمكن التراجع عنه.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.groups.destroy', $group->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@include('admin.groups.student_plan_scripts')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="mb-4 card">
            <div class="card-header">
                <h5 class="card-title">معلومات الحلقة</h5>
                 <a href="{{ route('admin.enrollments.create', ['group_id' => $group->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> إضافة خطة للطالب
                </a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 30%">اسم الحلقة</th>
                            <td>{{ $group->name }}</td>
                        </tr>
                        <tr>
                            <th>المادة الدراسية</th>
                            <td>
                                @if($group->subject)
                                    <a href="{{ route('admin.subjects.show', $group->subject_id) }}">{{ $group->subject->name }}</a>
                                @else
                                    غير محدد
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>المعلم</th>
                            <td>
                                @if($group->teacher)
                                    <a href="{{ route('admin.teachers.show', $group->teacher_id) }}">{{ $group->teacher->name }}</a>
                                @else
                                    غير محدد
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>الفرع</th>
                            <td>
                                @if($group->branch)
                                    <a href="{{ route('admin.branches.show', $group->branch_id) }}">{{ $group->branch->name }}</a>
                                @else
                                    غير محدد
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>جدول الحلقة</th>
                            <td>{{ $group->schedule ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>مكان الحلقة</th>
                            <td>{{ $group->location ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ البدء</th>
                            <td>{{ $group->start_date ? $group->start_date->format('Y-m-d') : 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ الانتهاء</th>
                            <td>{{ $group->end_date ? $group->end_date->format('Y-m-d') : 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>السعة القصوى</th>
                            <td>{{ $group->capacity ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>عدد الطلاب المسجلين</th>
                            <td>{{ $group->enrollments->count() }}</td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                @if($group->status == 'active')
                                    <span class="badge bg-success">نشط</span>
                                @elseif($group->status == 'completed')
                                    <span class="badge bg-info">مكتمل</span>
                                @elseif($group->status == 'cancelled')
                                    <span class="badge bg-danger">ملغي</span>
                                @else
                                    <span class="badge bg-secondary">{{ $group->status }}</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($group->description || $group->notes)
        <div class="mb-4 card">
            <div class="card-header">
                <h5 class="card-title">تفاصيل إضافية</h5>
            </div>
            <div class="card-body">
                @if($group->description)
                <div class="mb-4">
                    <h6 class="fw-bold">وصف الحلقة:</h6>
                    <p>{{ $group->description }}</p>
                </div>
                @endif

                @if($group->notes)
                <div>
                    <h6 class="fw-bold">ملاحظات إضافية:</h6>
                    <p>{{ $group->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">الطلاب المسجلين في الحلقة</h5>
                <a href="{{ route('admin.enrollments.create', ['group_id' => $group->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> إضافة طالب جديد
                </a>
            </div>
            <div class="card-body">
                @if($group->enrollments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الطالب</th>
                                <th>تاريخ التسجيل</th>
                                <th>نسبة الحضور</th>
                                <th>متوسط الدرجات</th>
                                <th>الخطة الشهرية</th>
                                <th>الانجاز اليومي</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group->enrollments as $enrollment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($enrollment->student)
                                        <a href="{{ route('admin.students.show', $enrollment->student_id) }}">
                                            {{ $enrollment->student->name }}
                                        </a>
                                    @else
                                        طالب {{ $enrollment->student_id }}
                                    @endif
                                </td>
                                <td>{{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('Y-m-d') : 'غير محدد' }}</td>
                                <td>
                                    @php
                                        $attendanceCount = $enrollment->student ? $enrollment->student->attendance()->where('group_id', $group->id)->count() : 0;
                                        $presentCount = $enrollment->student ? $enrollment->student->attendance()->where('group_id', $group->id)->where('status', 'present')->count() : 0;
                                        $attendanceRate = $attendanceCount > 0 ? round(($presentCount / $attendanceCount) * 100) : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar {{ $attendanceRate >= 75 ? 'bg-success' : ($attendanceRate >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                             role="progressbar" style="width: {{ $attendanceRate }}%;"
                                             aria-valuenow="{{ $attendanceRate }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $attendanceRate }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $grades = $enrollment->student ? $enrollment->student->grades()->where('group_id', $group->id)->get() : collect([]);
                                        $avgScore = $grades->count() > 0 ? round($grades->avg('grade')) : 0;
                                    @endphp
                                    <span class="badge {{ $avgScore >= 90 ? 'bg-success' : ($avgScore >= 70 ? 'bg-info' : ($avgScore >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                        {{ $avgScore }}
                                    </span>
                                </td>
                                <td>

                                    {{$group->studyPlan->lessons_count??'غير محدد'}} صفحة او درس
                                </td>
                                <td>
                                    {{$group->studyPlan->min_performance??'غير محدد'}} صفحة او درس
                                </td>

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
                                                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewEnrollmentModal{{ $enrollment->id }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEnrollmentModal{{ $enrollment->id }}">
                                                                                <i class="fas fa-edit"></i>
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEnrollmentModal{{ $enrollment->id }}">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                    </div>
                                                                        <!-- View Enrollment Modal -->
                                                                        <div class="modal fade" id="viewEnrollmentModal{{ $enrollment->id }}" tabindex="-1" aria-hidden="true">
                                                                            <div class="modal-dialog modal-lg">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title">عرض بيانات الطالب</h5>
                                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        @if($enrollment->student)
                                                                                            <p><strong>اسم الطالب:</strong> {{ $enrollment->student->name }}</p>
                                                                                            <p><strong>تاريخ التسجيل:</strong> {{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('Y-m-d') : 'غير محدد' }}</p>
                                                                                            <p><strong>الحالة:</strong> {{ $enrollment->status }}</p>
                                                                                            <!-- أضف المزيد من التفاصيل حسب الحاجة -->
                                                                                        @else
                                                                                            <p>لا توجد بيانات لهذا الطالب.</p>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- Edit Enrollment Modal -->
                                                                        <div class="modal fade" id="editEnrollmentModal{{ $enrollment->id }}" tabindex="-1" aria-hidden="true">
                                                                            <div class="modal-dialog modal-lg">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title">تعديل بيانات التسجيل</h5>
                                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <form action="{{ route('admin.enrollments.update', $enrollment->id) }}" method="POST">
                                                                                            @csrf
                                                                                            @method('PUT')
                                                                                            <div class="mb-3">
                                                                                                <label class="form-label">الحالة</label>
                                                                                                <select name="status" class="form-control">
                                                                                                    <option value="active" {{ $enrollment->status == 'active' ? 'selected' : '' }}>نشط</option>
                                                                                                    <option value="completed" {{ $enrollment->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                                                                                    <option value="dropped" {{ $enrollment->status == 'dropped' ? 'selected' : '' }}>منسحب</option>
                                                                                                </select>
                                                                                            </div>
                                                                                            <!-- أضف المزيد من الحقول حسب الحاجة -->
                                                                                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                    <!-- Delete Enrollment Modal -->
                                    <div class="modal fade" id="deleteEnrollmentModal{{ $enrollment->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">تأكيد إلغاء التسجيل</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من رغبتك في إلغاء تسجيل الطالب "{{ $enrollment->student->name ?? '' }}" من هذه الحلقة؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <form action="{{ route('admin.enrollments.destroy', $enrollment->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
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
                @else
                <div class="alert alert-info">
                    لا يوجد طلاب مسجلين في هذه الحلقة حتى الآن.
                </div>
                @endif
            </div>
        </div>
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">خطة الطالب</h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                    <i class="fas fa-plus-circle me-1"></i> إضافة خطة جديدة
                </button>
            </div>
            <div class="card-body">
                @if($studentPlans && $studentPlans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الطالب</th>
                                <th>الحفظ من </th>
                                <th>الحفظ الى  </th>
                                <th>المراجعة من  </th>
                                <th>المراجعة الى </th>
                                <th>الشهر  </th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentPlans as $plan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($plan->student)
                                        <a href="{{ route('admin.students.show', $plan->student_id) }}">
                                            {{ $plan->student->name }}
                                        </a>
                                    @else
                                        طالب {{ $plan->student_id }}
                                    @endif
                                </td>
                                <td>{{$plan->saving_from}}</td>
                                <td>{{$plan->saving_to}}</td>
                                <td>{{$plan->review_from}}</td>
                                <td>{{$plan->review_to}}</td>
                                <td>{{$plan->month}}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-info view-plan" data-bs-toggle="modal" data-bs-target="#viewPlanModal" 
                                            data-id="{{ $plan->id }}" 
                                            data-student="{{ $plan->student->name ?? 'طالب ' . $plan->student_id }}" 
                                            data-saving-from="{{ $plan->saving_from }}" 
                                            data-saving-to="{{ $plan->saving_to }}" 
                                            data-review-from="{{ $plan->review_from }}" 
                                            data-review-to="{{ $plan->review_to }}" 
                                            data-month="{{ $plan->month }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning edit-plan" data-bs-toggle="modal" data-bs-target="#editPlanModal" 
                                            data-id="{{ $plan->id }}" 
                                            data-student-id="{{ $plan->student_id }}" 
                                            data-saving-from="{{ $plan->saving_from }}" 
                                            data-saving-to="{{ $plan->saving_to }}" 
                                            data-review-from="{{ $plan->review_from }}" 
                                            data-review-to="{{ $plan->review_to }}" 
                                            data-month="{{ $plan->month }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deletePlanModal{{ $plan->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Plan Modal -->
                                    <div class="modal fade" id="deletePlanModal{{ $plan->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">تأكيد حذف الخطة</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من رغبتك في حذف خطة الطالب "{{ $plan->student->name ?? 'طالب ' . $plan->student_id }}"؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <form action="{{ route('admin.student_plans.destroy', $plan->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
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
                @else
                <div class="alert alert-info">
                    لا توجد خطط للطلاب في هذه الحلقة حتى الآن.
                </div>
                @endif
            </div>
        </div>

        <!-- Create Plan Modal -->
        <div class="modal fade" id="createPlanModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة خطة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <form action="{{ route('admin.student_plans.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="group_id" value="{{ $group->id }}">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">الطالب</label>
                                <select name="student_id" id="student_id" class="form-select" required>
                                    <option value="">اختر الطالب</option>
                                    @foreach($group->students as $student)
                                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="saving_from" class="form-label">الحفظ من</label>
                                    <input type="text" class="form-control" id="saving_from" name="saving_from" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="saving_to" class="form-label">الحفظ إلى</label>
                                    <input type="text" class="form-control" id="saving_to" name="saving_to" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="review_from" class="form-label">المراجعة من</label>
                                    <input type="text" class="form-control" id="review_from" name="review_from" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="review_to" class="form-label">المراجعة إلى</label>
                                    <input type="text" class="form-control" id="review_to" name="review_to" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="month" class="form-label">الشهر</label>
                                <input type="text" class="form-control" id="month" name="month" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Plan Modal -->
        <div class="modal fade" id="editPlanModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل خطة الطالب</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <form id="editPlanForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_student_id" class="form-label">الطالب</label>
                                <select name="student_id" id="edit_student_id" class="form-select" required>
                                    <option value="">اختر الطالب</option>
                                    @foreach($group->students as $student)
                                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_saving_from" class="form-label">الحفظ من</label>
                                    <input type="text" class="form-control" id="edit_saving_from" name="saving_from" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_saving_to" class="form-label">الحفظ إلى</label>
                                    <input type="text" class="form-control" id="edit_saving_to" name="saving_to" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_review_from" class="form-label">المراجعة من</label>
                                    <input type="text" class="form-control" id="edit_review_from" name="review_from" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_review_to" class="form-label">المراجعة إلى</label>
                                    <input type="text" class="form-control" id="edit_review_to" name="review_to" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_month" class="form-label">الشهر</label>
                                <input type="text" class="form-control" id="edit_month" name="month" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Plan Modal -->
        <div class="modal fade" id="viewPlanModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تفاصيل خطة الطالب</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">اسم الطالب:</div>
                            <div class="col-md-8" id="view_student"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">الحفظ من:</div>
                            <div class="col-md-8" id="view_saving_from"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">الحفظ إلى:</div>
                            <div class="col-md-8" id="view_saving_to"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">المراجعة من:</div>
                            <div class="col-md-8" id="view_review_from"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">المراجعة إلى:</div>
                            <div class="col-md-8" id="view_review_to"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">الشهر:</div>
                            <div class="col-md-8" id="view_month"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-4 card">
            <div class="card-header">
                <h5 class="card-title">إحصائيات الحلقة</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-6">
                        <div class="text-white card bg-primary">
                            <div class="text-center card-body">
                                <h6 class="card-title">الطلاب</h6>
                                <p class="card-text display-6">{{ $group->enrollments->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                        <div class="text-white card bg-success">
                            <div class="text-center card-body">
                                <h6 class="card-title">نسبة الحضور</h6>
                                <p class="card-text display-6">
                                    @php
                                        $attendanceCount = \App\Models\Attendance::where('group_id', $group->id)->count();
                                        $presentCount = \App\Models\Attendance::where('group_id', $group->id)->where('status', 'present')->count();
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
                                <p class="card-text display-6">{{ \App\Models\Grade::where('group_id', $group->id)->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                        <div class="text-white card bg-warning">
                            <div class="text-center card-body">
                                <h6 class="card-title">متوسط الدرجات</h6>
                                <p class="card-text display-6">
                                    @php
                                        $grades = \App\Models\Grade::where('group_id', $group->id)->get();
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

        <div class="mb-4 card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">الحضور الأخير</h5>
                <a href="{{ route('admin.attendance.create', ['group_id' => $group->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> تسجيل الحضور
                </a>
            </div>
            <div class="card-body">
                @php
                    $recentAttendance = \App\Models\Attendance::where('group_id', $group->id)
                        ->orderBy('date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get()
                        ->groupBy('date');
                @endphp

                @if($recentAttendance->count() > 0)
                    @foreach($recentAttendance as $date => $attendances)
                    <div class="mb-3 card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</h6>
                        </div>
                        <div class="p-0 card-body">
                            <div class="list-group list-group-flush">
                                @foreach($attendances as $attendance)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($attendance->student)
                                            <a href="{{ route('admin.students.show', $attendance->student_id) }}">
                                                {{ $attendance->student->name }}
                                            </a>
                                        @else
                                            طالب {{ $attendance->student_id }}
                                        @endif
                                    </div>
                                    <div>
                                        @if($attendance->status == 'present')
                                            <span class="badge bg-success">حاضر</span>
                                        @elseif($attendance->status == 'absent')
                                            <span class="badge bg-danger">غائب</span>
                                        @elseif($attendance->status == 'late')
                                            <span class="badge bg-warning">متأخر</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="mt-3 d-flex justify-content-end">
                        <a href="{{ route('admin.attendance.index', ['group_id' => $group->id]) }}" class="btn btn-sm btn-outline-primary">
                            عرض سجل الحضور الكامل
                        </a>
                    </div>
                @else
                <div class="alert alert-info">
                    لا يوجد سجل حضور لهذه الحلقة حتى الآن.
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">أحدث التقييمات</h5>
                <a href="{{ route('admin.grades.create', ['group_id' => $group->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> إضافة تقييم
                </a>
            </div>
            <div class="card-body">
                @php
                    $recentGrades = \App\Models\Grade::where('group_id', $group->id)
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp

                @if($recentGrades->count() > 0)
                <div class="list-group">
                    @foreach($recentGrades as $grade)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <a href="{{ route('admin.students.show', $grade->student_id) }}">
                                    {{ $grade->student->name }}
                                </a>
                            </h6>
                            <small>{{ $grade->date ? $grade->date->format('Y-m-d') : '' }}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-1">{{ Str::limit($grade->notes, 50) ?? 'لا توجد ملاحظات' }}</p>
                            <span class="badge {{ $grade->grade >= 90 ? 'bg-success' : ($grade->grade >= 70 ? 'bg-info' : ($grade->grade >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $grade->grade }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    <a href="{{ route('admin.grades.index', ['group_id' => $group->id]) }}" class="btn btn-sm btn-outline-primary">
                        عرض جميع التقييمات
                    </a>
                </div>
                @else
                <div class="alert alert-info">
                    لا توجد تقييمات لهذه الحلقة حتى الآن.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@include('admin.groups.student_plan_scripts')
