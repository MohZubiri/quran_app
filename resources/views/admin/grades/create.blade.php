@extends('layouts.admin')

@section('title', 'إضافة تقييمات جديدة')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.grades.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى سجل التقييمات
    </a>
    @if(request()->has('group_id'))
    <a href="{{ route('admin.groups.show', request()->group_id) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى الحلقة
    </a>
    @endif
    @if(request()->has('student_id'))
    <a href="{{ route('admin.students.show', request()->student_id) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى الطالب
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4">إضافة تقييمات جديدة</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.grades.store') }}" method="POST">
                @csrf
                
                @if(isset($defaultBranch))
                <!-- حقل مخفي للفرع عندما يكون هناك فرع واحد فقط -->
                <input type="hidden" name="branch_id" value="{{ $defaultBranch->id }}">
                <div class="alert alert-info mb-3">
                    تم تحديد الفرع تلقائياً: <strong>{{ $defaultBranch->name }}</strong>
                </div>
                @endif

                <!-- Student Selection -->
                <div class="mb-3">
                    <label for="student_id" class="form-label">الطالب</label>
                    <select class="form-select" id="student_id" name="student_id" required>
                        <option value="">اختر الطالب</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" 
                                    data-group-id="{{ optional($student->activeGroup())->id }}"
                                    {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Group Selection -->
                <div class="mb-3">
                    <label for="group_id" class="form-label">المجموعة</label>
                    @if(isset($preselectedGroup))
                        <input type="hidden" name="group_id" value="{{ $preselectedGroup->id }}">
                        <select class="form-select" id="group_id" disabled>
                            <option value="{{ $preselectedGroup->id }}" selected>{{ $preselectedGroup->name }}</option>
                        </select>
                        <small class="form-text text-muted">تم تحديد المجموعة تلقائيًا من الرابط</small>
                    @else
                        <select class="form-select" id="group_id" name="group_id" required>
                            <option value="">اختر المجموعة</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('group_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Teacher Selection -->
                <div class="mb-3">
                    <label for="teacher_id" class="form-label">المعلم</label>
                    @if(isset($preselectedTeacherFromGroup))
                        <input type="hidden" name="teacher_id" value="{{ $preselectedTeacherFromGroup }}">
                        <select class="form-select" id="teacher_id" disabled>
                            <option value="{{ $preselectedTeacherFromGroup }}" selected>
                                {{ $teachers->find($preselectedTeacherFromGroup)->name ?? 'المعلم المحدد' }}
                            </option>
                        </select>
                        <small class="form-text text-muted">تم تحديد المعلم تلقائيًا بناءً على المجموعة المختارة</small>
                    @elseif(isset($preselectedTeacher))
                        <input type="hidden" name="teacher_id" value="{{ $preselectedTeacher }}">
                        <select class="form-select" id="teacher_id" disabled>
                            <option value="{{ $preselectedTeacher }}" selected>
                                {{ $teachers->find($preselectedTeacher)->name ?? 'المعلم المحدد' }}
                            </option>
                        </select>
                        <small class="form-text text-muted">تم تحديد المعلم تلقائيًا بناءً على حسابك</small>
                    @else
                        <select class="form-select" id="teacher_id" name="teacher_id" required>
                            <option value="">اختر المعلم</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('teacher_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Subject Selection -->
                <div class="mb-3">
                    <label for="subject_id" class="form-label">المادة</label>
                    @if(isset($preselectedSubject))
                        <input type="hidden" name="subject_id" value="{{ $preselectedSubject }}">
                        <select class="form-select" id="subject_id" disabled>
                            <option value="{{ $preselectedSubject }}" selected>
                                {{ $subjects->find($preselectedSubject)->name ?? 'المادة المحددة' }}
                            </option>
                        </select>
                        <small class="form-text text-muted">تم تحديد المادة تلقائيًا بناءً على المجموعة المختارة</small>
                    @else
                        <select class="form-select" id="subject_id" name="subject_id" required>
                            <option value="">اختر المادة</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('subject_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Date -->
                <div class="mb-3">
                    <label for="date" class="form-label">التاريخ</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                    @error('date')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <!-- Grade Types -->
                <div class="row">
                    <!-- Achievement -->
                    <div class="col-md-2 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">تقييم الإنجاز</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="achievement_score" class="form-label">الدرجة</label>
                                    <input type="number" class="form-control" id="achievement_score" name="grades[achievement][grade]" min="0" max="100" step="0.5" value="{{ old('grades.achievement.grade') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="achievement_notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control" id="achievement_notes" name="grades[achievement][notes]" rows="2">{{ old('grades.achievement.notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Behavior -->
                    <div class="col-md-2 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">تقييم السلوك</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="behavior_score" class="form-label">الدرجة</label>
                                    <input type="number" class="form-control" id="behavior_score" name="grades[behavior][grade]" min="0" max="100" step="0.5" value="{{ old('grades.behavior.grade') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="behavior_notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control" id="behavior_notes" name="grades[behavior][notes]" rows="2">{{ old('grades.behavior.notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance -->
                    <div class="col-md-2 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">تقييم الحضور</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="attendance_score" class="form-label">الدرجة</label>
                                    <input type="number" class="form-control" id="attendance_score" name="grades[attendance][grade]" min="0" max="100" step="0.5" value="{{ old('grades.attendance.grade') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="attendance_notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control" id="attendance_notes" name="grades[attendance][notes]" rows="2">{{ old('grades.attendance.notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appearance -->
                    <div class="col-md-2 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-warning">
                                <h5 class="card-title mb-0">تقييم المظهر</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="appearance_score" class="form-label">الدرجة</label>
                                    <input type="number" class="form-control" id="appearance_score" name="grades[appearance][grade]" min="0" max="100" step="0.5" value="{{ old('grades.appearance.grade') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="appearance_notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control" id="appearance_notes" name="grades[appearance][notes]" rows="2">{{ old('grades.appearance.notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Plan Score (الإنجاز اليومي من الخطة) -->
                    <div class="col-md-2 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-purple text-white" style="background-color: #6f42c1;">
                                <h5 class="card-title mb-0">الإنجاز اليومي من الخطة</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="plan_score_score" class="form-label">الدرجة</label>
                                    <input type="number" class="form-control" id="plan_score_score" name="grades[plan_score][grade]" min="0" max="100" step="0.5" value="{{ old('grades.plan_score.grade') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="plan_score_notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control" id="plan_score_notes" name="grades[plan_score][notes]" rows="2">{{ old('grades.plan_score.notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i> حفظ جميع التقييمات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('student_id');
    const groupSelect = document.getElementById('group_id');
    
    // Solo aplicar la lógica si los elementos no están deshabilitados
    if (studentSelect && groupSelect && !groupSelect.disabled) {
        // Función para filtrar estudiantes por grupo seleccionado
        function filterStudentsByGroup(groupId) {
            // Ocultar todos los estudiantes primero
            for (let i = 0; i < studentSelect.options.length; i++) {
                const option = studentSelect.options[i];
                const studentGroupId = option.dataset.groupId;
                
                // Si el estudiante pertenece al grupo seleccionado o no hay grupo seleccionado, mostrar
                if (!groupId || studentGroupId == groupId || i === 0) { // Siempre mostrar la opción "Seleccionar estudiante"
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            }
        }
        
        // Si hay un grupo seleccionado, filtrar estudiantes
        if (groupSelect.value) {
            filterStudentsByGroup(groupSelect.value);
        }

        // Update group when student changes
        studentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const groupId = selectedOption.dataset.groupId;
            
            if (groupId) {
                groupSelect.value = groupId;
            }
        });
        
        // Filtrar estudiantes cuando cambia el grupo
        groupSelect.addEventListener('change', function() {
            filterStudentsByGroup(this.value);
        });
    }
    
    // Si hay un grupo preseleccionado, mostrar solo los estudiantes de ese grupo
    if (groupSelect && groupSelect.disabled && studentSelect) {
        const groupId = document.querySelector('input[name="group_id"]').value;
        
        // Filtrar estudiantes para mostrar solo los del grupo preseleccionado
        for (let i = 0; i < studentSelect.options.length; i++) {
            const option = studentSelect.options[i];
            const studentGroupId = option.dataset.groupId;
            
            if (!studentGroupId || studentGroupId == groupId || i === 0) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        }
    }
});
</script>
@endpush
@endsection
