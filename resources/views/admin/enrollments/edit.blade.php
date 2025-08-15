@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4">تعديل التسجيل</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.enrollments.update', $enrollment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Student Information (Read-only) -->
                <div class="mb-3">
                    <label class="form-label">الطالب</label>
                    <input type="text" class="form-control" value="{{ $enrollment->student->name }}" readonly>
                    <input type="hidden" name="student_id" value="{{ $enrollment->student_id }}">
                </div>

                <!-- Branch Selection -->
                <div class="mb-3">
                    <label for="branch_id" class="form-label">الفرع</label>
                    @if(!$defaultBranch)
                        <select class="form-select" id="branch_id" name="branch_id" required>
                            <option value="">اختر الفرع</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $enrollment->group->branch_id == $branch->id ? 'selected' : '' }}>
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
                            <option value="{{ $group->id }}" data-branch="{{ $group->branch_id }}" {{ $enrollment->group_id == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('group_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="active" {{ $enrollment->status == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ $enrollment->status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="mb-3">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" name="notes" rows="3">{{ old('notes', $enrollment->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
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
    const groupOptions = Array.from(groupSelect.options);

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

    // Branch change event
    branchSelect.addEventListener('change', function() {
        const selectedBranchId = this.value;
        filterGroupsByBranch(selectedBranchId);
    });

    // Initial filtering if branch is pre-selected
    if (branchSelect.value) {
        filterGroupsByBranch(branchSelect.value);
    }
});
</script>
@endpush
@endsection
