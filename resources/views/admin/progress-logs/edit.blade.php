@extends('layouts.admin')

@section('title', 'تعديل سجل التقدم')

@section('actions')
<a href="{{ route('admin.progress-logs.index') }}" class="btn btn-sm btn-secondary">
    <i class="fas fa-arrow-right me-1"></i> العودة إلى سجلات التقدم
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">تعديل سجل التقدم</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.progress-logs.update', $progressLog->id) }}" method="POST">
            @csrf
            @method('PUT')
            
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
                                <option value="{{ $branch->id }}" {{ old('branch_id', $progressLog->student->group->branch_id) == $branch->id ? 'selected' : '' }}>
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
                            <option value="{{ $group->id }}" data-branch="{{ $group->branch_id }}" {{ old('group_id', $progressLog->group_id) == $group->id ? 'selected' : '' }}>
                                {{ $group->name }} ({{ optional($group->teacher)->name ?? 'بدون مدرس' }})
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
                            <option value="{{ $student->id }}" {{ old('student_id', $progressLog->student_id) == $student->id ? 'selected' : '' }}>
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
                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $progressLog->date->format('Y-m-d')) }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="surah_name" class="form-label">اسم السورة <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('surah_name') is-invalid @enderror" id="surah_name" name="surah_name" value="{{ old('surah_name', $progressLog->surah_name) }}" required>
                    @error('surah_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="from_verse" class="form-label">من آية <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('from_verse') is-invalid @enderror" id="from_verse" name="from_verse" value="{{ old('from_verse', $progressLog->from_verse) }}" min="1" required>
                    @error('from_verse')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="to_verse" class="form-label">إلى آية <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('to_verse') is-invalid @enderror" id="to_verse" name="to_verse" value="{{ old('to_verse', $progressLog->to_verse) }}" min="1" required>
                    @error('to_verse')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="memorization_quality" class="form-label">جودة الحفظ (1-10) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('memorization_quality') is-invalid @enderror" id="memorization_quality" name="memorization_quality" value="{{ old('memorization_quality', $progressLog->memorization_quality) }}" min="1" max="10" required>
                    @error('memorization_quality')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="tajweed_quality" class="form-label">جودة التجويد (1-10) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('tajweed_quality') is-invalid @enderror" id="tajweed_quality" name="tajweed_quality" value="{{ old('tajweed_quality', $progressLog->tajweed_quality) }}" min="1" max="10" required>
                    @error('tajweed_quality')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="revision_quality" class="form-label">جودة المراجعة (1-10)</label>
                    <input type="number" class="form-control @error('revision_quality') is-invalid @enderror" id="revision_quality" name="revision_quality" value="{{ old('revision_quality', $progressLog->revision_quality) }}" min="1" max="10">
                    @error('revision_quality')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="">-- اختر الحالة --</option>
                    <option value="excellent" {{ old('status', $progressLog->status) == 'excellent' ? 'selected' : '' }}>ممتاز</option>
                    <option value="good" {{ old('status', $progressLog->status) == 'good' ? 'selected' : '' }}>جيد</option>
                    <option value="needs_improvement" {{ old('status', $progressLog->status) == 'needs_improvement' ? 'selected' : '' }}>يحتاج إلى تحسين</option>
                    <option value="incomplete" {{ old('status', $progressLog->status) == 'incomplete' ? 'selected' : '' }}>غير مكتمل</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">ملاحظات</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $progressLog->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                <a href="{{ route('admin.progress-logs.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const branchSelect = document.getElementById('branch_id');
    const groupSelect = document.getElementById('group_id');
    const studentSelect = document.getElementById('student_id');
    
    // تصفية المجموعات حسب الفرع المختار
    function filterGroups() {
        const selectedBranchId = branchSelect ? branchSelect.value : null;
        
        if (selectedBranchId) {
            Array.from(groupSelect.options).forEach(option => {
                if (option.value === '') return; // تخطي خيار "اختر المجموعة"
                
                const groupBranchId = option.getAttribute('data-branch');
                option.style.display = (groupBranchId === selectedBranchId) ? '' : 'none';
            });
            
            // إعادة تعيين المجموعة المختارة إذا كانت غير متوافقة مع الفرع الجديد
            const visibleOptions = Array.from(groupSelect.options).filter(option => 
                option.value !== '' && option.style.display !== 'none'
            );
            
            if (visibleOptions.length > 0 && groupSelect.selectedOptions[0].style.display === 'none') {
                groupSelect.value = '';
            }
        }
    }
    
    // تصفية الطلاب حسب المجموعة المختارة
    function filterStudents() {
        const selectedGroupId = groupSelect.value;
        
        if (selectedGroupId) {
            // هنا يمكن إضافة منطق لتصفية الطلاب حسب المجموعة
            // مثلاً باستخدام AJAX للحصول على الطلاب في المجموعة المحددة
        }
    }
    
    // تطبيق التصفية عند تغيير الفرع
    if (branchSelect) {
        branchSelect.addEventListener('change', function() {
            filterGroups();
            filterStudents();
        });
    }
    
    // تطبيق التصفية عند تغيير المجموعة
    groupSelect.addEventListener('change', filterStudents);
    
    // تطبيق التصفية عند تحميل الصفحة
    if (branchSelect) {
        filterGroups();
    }
    filterStudents();
});
</script>
@endpush
@endsection
