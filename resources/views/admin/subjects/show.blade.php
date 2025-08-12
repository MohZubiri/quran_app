@extends('layouts.admin')

@section('title', 'تفاصيل المادة الدراسية')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.subjects.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى قائمة المواد الدراسية
    </a>
    <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit me-1"></i> تعديل المادة
    </a>
    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
        <i class="fas fa-trash me-1"></i> حذف المادة
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
                هل أنت متأكد من رغبتك في حذف المادة "{{ $subject->name }}"؟ هذا الإجراء لا يمكن التراجع عنه.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="d-inline">
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
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">معلومات المادة الدراسية</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 30%">اسم المادة</th>
                            <td>{{ $subject->name }}</td>
                        </tr>
                        <tr>
                            <th>الفرع</th>
                            <td>{{ $subject->branch->name ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>التصنيف</th>
                            <td>
                                @if($subject->category == 'quran')
                                    القرآن الكريم
                                @elseif($subject->category == 'tajweed')
                                    التجويد
                                @elseif($subject->category == 'tafsir')
                                    التفسير
                                @elseif($subject->category == 'memorization')
                                    الحفظ
                                @elseif($subject->category == 'recitation')
                                    التلاوة
                                @elseif($subject->category == 'other')
                                    أخرى
                                @else
                                    {{ $subject->category ?? 'غير محدد' }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>المستوى</th>
                            <td>
                                @if($subject->level == 'beginner')
                                    مبتدئ
                                @elseif($subject->level == 'intermediate')
                                    متوسط
                                @elseif($subject->level == 'advanced')
                                    متقدم
                                @else
                                    {{ $subject->level ?? 'غير محدد' }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>المدة</th>
                            <td>{{ $subject->duration ? $subject->duration . ' أسبوع' : 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                @if($subject->status == 'active')
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

        @if($subject->description || $subject->prerequisites || $subject->objectives)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">تفاصيل المادة</h5>
            </div>
            <div class="card-body">
                @if($subject->description)
                <div class="mb-4">
                    <h6 class="fw-bold">وصف المادة:</h6>
                    <p>{{ $subject->description }}</p>
                </div>
                @endif

                @if($subject->prerequisites)
                <div class="mb-4">
                    <h6 class="fw-bold">المتطلبات السابقة:</h6>
                    <p>{{ $subject->prerequisites }}</p>
                </div>
                @endif

                @if($subject->objectives)
                <div>
                    <h6 class="fw-bold">أهداف المادة:</h6>
                    <p>{{ $subject->objectives }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">الحلقات المرتبطة بالمادة</h5>
                <a href="{{ route('admin.groups.create', ['subject_id' => $subject->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> إضافة حلقة جديدة
                </a>
            </div>
            <div class="card-body">
                @if($subject->groups->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الحلقة</th>
                                <th>المعلم</th>
                                <th>الجدول</th>
                                <th>عدد الطلاب</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subject->groups as $group)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $group->name }}</td>
                                <td>{{ $group->teacher->name ?? '' }} </td>
                                <td>{{ $group->schedule ?? 'غير محدد' }}</td>
                                <td>{{ $group->enrollments->count() }}</td>
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
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.groups.show', $group->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.groups.edit', $group->id) }}" class="btn btn-sm btn-warning">
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
                    لا توجد حلقات مرتبطة بهذه المادة حتى الآن.
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">إحصائيات المادة</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">الحلقات</h6>
                                <p class="card-text display-6">{{ $subject->groups->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">الطلاب</h6>
                                <p class="card-text display-6">
                                    @php
                                        $studentCount = 0;
                                        foreach($subject->groups as $group) {
                                            $studentCount += $group->enrollments->count();
                                        }
                                    @endphp
                                    {{ $studentCount }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">المعلمين</h6>
                                <p class="card-text display-6">
                                    @php
                                        $teacherIds = $subject->groups->pluck('teacher_id')->unique()->filter()->count();
                                    @endphp
                                    {{ $teacherIds }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">متوسط الدرجات</h6>
                                <p class="card-text display-6">
                                    @php
                                        $avgScore = 0;
                                        $grades = \App\Models\Grade::whereHas('group', function($query) use ($subject) {
                                            $query->where('subject_id', $subject->id);
                                        })->get();
                                        
                                        if($grades->count() > 0) {
                                            $avgScore = round($grades->avg('grade'));
                                        }
                                    @endphp
                                    {{ $avgScore }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">المعلمين المرتبطين بالمادة</h5>
            </div>
            <div class="card-body">
                @php
                    $teachers = \App\Models\Teacher::whereHas('groups', function($query) use ($subject) {
                        $query->where('subject_id', $subject->id);
                    })->get();
                @endphp

                @if($teachers->count() > 0)
                <div class="list-group">
                    @foreach($teachers as $teacher)
                    <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-user-tie me-2"></i>
                            {{ $teacher->name }} 
                        </div>
                        <span class="badge bg-primary rounded-pill">
                            {{ $teacher->groups()->where('subject_id', $subject->id)->count() }} حلقة
                        </span>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="alert alert-info">
                    لا يوجد معلمين مرتبطين بهذه المادة حتى الآن.
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">أحدث التقييمات</h5>
            </div>
            <div class="card-body">
                @php
                    $recentGrades = \App\Models\Grade::whereHas('group', function($query) use ($subject) {
                        $query->where('subject_id', $subject->id);
                    })->latest()->take(5)->get();
                @endphp

                @if($recentGrades->count() > 0)
                <div class="list-group">
                    @foreach($recentGrades as $grade)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <a href="{{ route('admin.students.show', $grade->student_id) }}">
                                    {{ $grade->student->name ?? 'طالب ' . $grade->student_id  }} 
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
                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('admin.grades.index', ['subject_id' => $subject->id]) }}" class="btn btn-sm btn-outline-primary">
                        عرض جميع التقييمات
                    </a>
                </div>
                @else
                <div class="alert alert-info">
                    لا توجد تقييمات لهذه المادة حتى الآن.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
