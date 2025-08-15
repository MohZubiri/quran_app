@extends('layouts.admin')

@section('title', 'تعديل التقييم')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.grades.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى سجل التقييمات
    </a>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">تعديل التقييم</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.grades.update', $grade->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            @if(isset($defaultBranch))
            <!-- حقل مخفي للفرع عندما يكون هناك فرع واحد فقط -->
            <input type="hidden" name="branch_id" value="{{ $defaultBranch->id }}">
            <div class="alert alert-info mb-3">
                تم تحديد الفرع تلقائياً: <strong>{{ $defaultBranch->name }}</strong>
            </div>
            @endif
            
            <!-- Student Selection -->
            <div class="mb-3">
                <label for="student_id" class="form-label">الطالب <span class="text-danger">*</span></label>
                <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
                    <option value="">اختر الطالب</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id', $grade->student_id) == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Group Selection -->
            <div class="mb-3">
                <label for="group_id" class="form-label">الحلقة <span class="text-danger">*</span></label>
                <select class="form-select @error('group_id') is-invalid @enderror" id="group_id" name="group_id" required>
                    <option value="">اختر الحلقة</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ old('group_id', $grade->group_id) == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
                @error('group_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Subject Selection -->
            <div class="mb-3">
                <label for="subject_id" class="form-label">المادة <span class="text-danger">*</span></label>
                <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                    <option value="">اختر المادة</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $grade->subject_id) == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                @error('subject_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Teacher Selection -->
            <div class="mb-3">
                <label for="teacher_id" class="form-label">المعلم <span class="text-danger">*</span></label>
                <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                    <option value="">اختر المعلم</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $grade->teacher_id) == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
                @error('teacher_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Date Selection -->
            <div class="mb-3">
                <label for="date" class="form-label">تاريخ التقييم <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $grade->date->format('Y-m-d')) }}" required>
                @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Grade Types -->
            <div class="row">
                <!-- Achievement -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="card-title mb-0">الإنجاز</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="achievement_score" class="form-label">الدرجة</label>
                                <input type="number" class="form-control" id="achievement_score" name="grades[achievement][grade]" min="0" max="100" step="0.5" value="{{ isset($allGrades['achievement']) ? $allGrades['achievement']->grade : old('grades.achievement.grade') }}">
                            </div>
                            <div class="mb-3">
                                <label for="achievement_notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control" id="achievement_notes" name="grades[achievement][notes]" rows="2">{{ isset($allGrades['achievement']) ? $allGrades['achievement']->notes : old('grades.achievement.notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Behavior -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="card-title mb-0">السلوك</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="behavior_score" class="form-label">الدرجة</label>
                                <input type="number" class="form-control" id="behavior_score" name="grades[behavior][grade]" min="0" max="100" step="0.5" value="{{ isset($allGrades['behavior']) ? $allGrades['behavior']->grade : old('grades.behavior.grade') }}">
                            </div>
                            <div class="mb-3">
                                <label for="behavior_notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control" id="behavior_notes" name="grades[behavior][notes]" rows="2">{{ isset($allGrades['behavior']) ? $allGrades['behavior']->notes : old('grades.behavior.notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="card-title mb-0">الحضور</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="attendance_score" class="form-label">الدرجة</label>
                                <input type="number" class="form-control" id="attendance_score" name="grades[attendance][grade]" min="0" max="100" step="0.5" value="{{ isset($allGrades['attendance']) ? $allGrades['attendance']->grade : old('grades.attendance.grade') }}">
                            </div>
                            <div class="mb-3">
                                <label for="attendance_notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control" id="attendance_notes" name="grades[attendance][notes]" rows="2">{{ isset($allGrades['attendance']) ? $allGrades['attendance']->notes : old('grades.attendance.notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appearance -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="card-title mb-0">المظهر</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="appearance_score" class="form-label">الدرجة</label>
                                <input type="number" class="form-control" id="appearance_score" name="grades[appearance][grade]" min="0" max="100" step="0.5" value="{{ isset($allGrades['appearance']) ? $allGrades['appearance']->grade : old('grades.appearance.grade') }}">
                            </div>
                            <div class="mb-3">
                                <label for="appearance_notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control" id="appearance_notes" name="grades[appearance][notes]" rows="2">{{ isset($allGrades['appearance']) ? $allGrades['appearance']->notes : old('grades.appearance.notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Plan Score (الإنجاز اليومي من الخطة) -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header" style="background-color: #6f42c1; color: white;">
                            <h6 class="card-title mb-0">الإنجاز اليومي من الخطة</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="plan_score_score" class="form-label">الدرجة</label>
                                <input type="number" class="form-control" id="plan_score_score" name="grades[plan_score][grade]" min="0" max="100" step="0.5" value="{{ isset($allGrades['plan_score']) ? $allGrades['plan_score']->grade : old('grades.plan_score.grade') }}">
                            </div>
                            <div class="mb-3">
                                <label for="plan_score_notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control" id="plan_score_notes" name="grades[plan_score][notes]" rows="2">{{ isset($allGrades['plan_score']) ? $allGrades['plan_score']->notes : old('grades.plan_score.notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-1"></i> حذف التقييم
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
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
                هل أنت متأكد من رغبتك في حذف هذا التقييم؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.grades.destroy', $grade->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
