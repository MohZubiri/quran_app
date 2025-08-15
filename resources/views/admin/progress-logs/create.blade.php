@extends('layouts.admin')

@section('title', 'إضافة سجل تقدم جديد')

@section('actions')
<a href="{{ route('admin.progress-logs.index') }}" class="btn btn-sm btn-secondary">
    <i class="fas fa-arrow-right me-1"></i> العودة إلى سجلات التقدم
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">إضافة سجل تقدم جديد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.progress-logs.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="branch_id" class="form-label">الفرع <span class="text-danger">*</span></label>
                    @if(isset($defaultBranch))
                        <div class="alert alert-info">
                            تم تحديد الفرع تلقائياً: {{ $defaultBranch->name }}
                        </div>
                        <input type="hidden" name="branch_id" value="{{ $defaultBranch->id }}">
                    @else
                        <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                            <option value="">-- اختر الفرع --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="group_id" class="form-label">المجموعة <span class="text-danger">*</span></label>
                    <select class="form-select @error('group_id') is-invalid @enderror" id="group_id" name="group_id" required>
                        <option value="">-- اختر المجموعة --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }} ({{ $group->teacher->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('group_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="student_id" class="form-label">الطالب <span class="text-danger">*</span></label>
                    <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
                        <option value="">-- اختر الطالب --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="date" class="form-label">التاريخ <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="memorization" class="form-label">الحفظ <span class="text-danger">*</span></label>
                    <select class="form-select @error('memorization') is-invalid @enderror" id="memorization" name="memorization" required>
                        <option value="">-- اختر التقييم --</option>
                        <option value="ممتاز" {{ old('memorization') == 'ممتاز' ? 'selected' : '' }}>ممتاز</option>
                        <option value="جيد جداً" {{ old('memorization') == 'جيد جداً' ? 'selected' : '' }}>جيد جداً</option>
                        <option value="جيد" {{ old('memorization') == 'جيد' ? 'selected' : '' }}>جيد</option>
                        <option value="مقبول" {{ old('memorization') == 'مقبول' ? 'selected' : '' }}>مقبول</option>
                        <option value="ضعيف" {{ old('memorization') == 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
                    </select>
                    @error('memorization')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="revision" class="form-label">المراجعة <span class="text-danger">*</span></label>
                    <select class="form-select @error('revision') is-invalid @enderror" id="revision" name="revision" required>
                        <option value="">-- اختر التقييم --</option>
                        <option value="ممتاز" {{ old('revision') == 'ممتاز' ? 'selected' : '' }}>ممتاز</option>
                        <option value="جيد جداً" {{ old('revision') == 'جيد جداً' ? 'selected' : '' }}>جيد جداً</option>
                        <option value="جيد" {{ old('revision') == 'جيد' ? 'selected' : '' }}>جيد</option>
                        <option value="مقبول" {{ old('revision') == 'مقبول' ? 'selected' : '' }}>مقبول</option>
                        <option value="ضعيف" {{ old('revision') == 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
                    </select>
                    @error('revision')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="recitation" class="form-label">التلاوة <span class="text-danger">*</span></label>
                    <select class="form-select @error('recitation') is-invalid @enderror" id="recitation" name="recitation" required>
                        <option value="">-- اختر التقييم --</option>
                        <option value="ممتاز" {{ old('recitation') == 'ممتاز' ? 'selected' : '' }}>ممتاز</option>
                        <option value="جيد جداً" {{ old('recitation') == 'جيد جداً' ? 'selected' : '' }}>جيد جداً</option>
                        <option value="جيد" {{ old('recitation') == 'جيد' ? 'selected' : '' }}>جيد</option>
                        <option value="مقبول" {{ old('recitation') == 'مقبول' ? 'selected' : '' }}>مقبول</option>
                        <option value="ضعيف" {{ old('recitation') == 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
                    </select>
                    @error('recitation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="tajweed" class="form-label">التجويد <span class="text-danger">*</span></label>
                    <select class="form-select @error('tajweed') is-invalid @enderror" id="tajweed" name="tajweed" required>
                        <option value="">-- اختر التقييم --</option>
                        <option value="ممتاز" {{ old('tajweed') == 'ممتاز' ? 'selected' : '' }}>ممتاز</option>
                        <option value="جيد جداً" {{ old('tajweed') == 'جيد جداً' ? 'selected' : '' }}>جيد جداً</option>
                        <option value="جيد" {{ old('tajweed') == 'جيد' ? 'selected' : '' }}>جيد</option>
                        <option value="مقبول" {{ old('tajweed') == 'مقبول' ? 'selected' : '' }}>مقبول</option>
                        <option value="ضعيف" {{ old('tajweed') == 'ضعيف' ? 'selected' : '' }}>ضعيف</option>
                    </select>
                    @error('tajweed')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">حفظ</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Branch change handler
    const branchSelect = document.getElementById('branch_id');
    const groupSelect = document.getElementById('group_id');
    const studentSelect = document.getElementById('student_id');

    // Update groups when branch changes
    branchSelect.addEventListener('change', function() {
        const branchId = this.value;
        
        // Reset group and student selects
        groupSelect.innerHTML = '<option value="">-- اختر المجموعة --</option>';
        studentSelect.innerHTML = '<option value="">-- اختر الطالب --</option>';
        
        if (branchId) {
            // Filter groups by branch
            const branchGroups = @json($groups->groupBy('branch_id'));
            if (branchGroups[branchId]) {
                branchGroups[branchId].forEach(group => {
                    const option = new Option(
                        `${group.name} (${group.teacher.name})`, 
                        group.id, 
                        false, 
                        {{ old('group_id') }} == group.id
                    );
                    groupSelect.add(option);
                });
            }
        }
    });

    // Update students when group changes
    groupSelect.addEventListener('change', function() {
        const groupId = this.value;
        
        // Reset student select
        studentSelect.innerHTML = '<option value="">-- اختر الطالب --</option>';
        
        if (groupId) {
            // Filter students by group
            const groupStudents = @json($students->groupBy('group_id'));
            if (groupStudents[groupId]) {
                groupStudents[groupId].forEach(student => {
                    const option = new Option(
                        student.name, 
                        student.id, 
                        false, 
                        {{ old('student_id') }} == student.id
                    );
                    studentSelect.add(option);
                });
            }
        }
    });

    // Trigger change events if there are old values
    if (branchSelect.value) {
        branchSelect.dispatchEvent(new Event('change'));
        if (groupSelect.value) {
            groupSelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endpush
@endsection
