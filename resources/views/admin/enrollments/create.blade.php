@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4">إضافة تسجيل جديد</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.enrollments.store') }}" method="POST">
                @csrf

                <!-- Branch Selection -->
                <div class="mb-3">
                    <label for="branch_id" class="form-label">الفرع</label>
                    @if(!$defaultBranch)
                        <select class="form-select" id="branch_id" name="branch_id" required>
                            <option value="">اختر الفرع</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <select class="form-select" id="branch_id" disabled>
                            <option value="{{ $defaultBranch->id }}" selected>{{ $defaultBranch->name }}</option>
                        </select>
                        <input type="hidden" name="branch_id" value="{{ $defaultBranch->id }}">
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-info-circle"></i> تم تحديد الفرع تلقائياً: {{ $defaultBranch->name }}
                        </div>
                    @endif
                    @error('branch_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Group Selection -->
                <div class="mb-3">
                    <label for="group_id" class="form-label">المجموعة</label>
                    <select class="form-select" id="group_id" name="group_id" required>
                        <option value="">اختر المجموعة</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" data-branch="{{ $group->branch_id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('group_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Student Selection -->
                <div class="mb-3">
                    <label for="student_id" class="form-label">الطالب</label>
                    <select class="form-select" id="student_id" name="student_id" required>
                        <option value="">اختر الطالب</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" data-branch="{{ $student->branch_id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Enrollment Date -->
                <div class="mb-3">
                    <label for="enrollment_date" class="form-label">تاريخ التسجيل <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('enrollment_date') is-invalid @enderror" 
                           id="enrollment_date" name="enrollment_date" 
                           value="{{ old('enrollment_date', now()->format('Y-m-d')) }}" required>
                    @error('enrollment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="mb-3">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ</button>
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const branchSelect = document.getElementById('branch_id');
    const groupSelect = document.getElementById('group_id');
    const studentSelect = document.getElementById('student_id');
    
    // Store original options
    const groupOptions = Array.from(groupSelect.options);
    const studentOptions = Array.from(studentSelect.options);

    // Function to filter groups by branch
    function filterGroupsByBranch(branchId) {
        // Reset group select
        groupSelect.innerHTML = '<option value="">اختر المجموعة</option>';
        
        // Filter and add relevant groups
        groupOptions.forEach(option => {
            if (!option.value) return; // Skip placeholder option
            
            if (option.dataset.branch === branchId) {
                groupSelect.appendChild(option.cloneNode(true));
            }
        });
    }

    // Function to filter students by branch
    function filterStudentsByBranch(branchId) {
        // Reset student select
        studentSelect.innerHTML = '<option value="">اختر الطالب</option>';
        
        // Filter and add relevant students
        studentOptions.forEach(option => {
            if (!option.value) return; // Skip placeholder option
            
            if (option.dataset.branch === branchId) {
                studentSelect.appendChild(option.cloneNode(true));
            }
        });
    }

    // Branch change event
    branchSelect.addEventListener('change', function() {
        const selectedBranchId = this.value;
        
        // Filter groups and students by branch
        filterGroupsByBranch(selectedBranchId);
        filterStudentsByBranch(selectedBranchId);
    });

    // Initial filtering if branch is pre-selected
    if (branchSelect.value) {
        filterGroupsByBranch(branchSelect.value);
        filterStudentsByBranch(branchSelect.value);
    }
});
</script>
@endpush
@endsection
