@extends('layouts.admin')

@section('title', 'تعديل سجل الحضور')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى سجل الحضور
    </a>
    @if($attendance->group)
    <a href="{{ route('admin.groups.show', $attendance->group_id) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى الحلقة
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">تعديل سجل الحضور</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="student_name" class="form-label">الطالب</label>
                    <input type="text" class="form-control" id="student_name" value="{{ $attendance->student->name }} " readonly>
                    <input type="hidden" name="student_id" value="{{ $attendance->student_id }}">
                </div>
                
                <div class="col-md-4">
                    <label for="group_name" class="form-label">الحلقة</label>
                    <input type="text" class="form-control" id="group_name" value="{{ $attendance->group->name }}" readonly>
                    <input type="hidden" name="group_id" value="{{ $attendance->group_id }}">
                </div>
                
                <div class="col-md-4">
                    <label for="date" class="form-label">تاريخ الحضور <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $attendance->date->format('Y-m-d')) }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status" class="form-label">حالة الحضور <span class="text-danger">*</span></label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="status" id="status_present" value="present" {{ old('status', $attendance->status) == 'present' ? 'checked' : '' }} autocomplete="off">
                        <label class="btn btn-outline-success" for="status_present">حاضر</label>
                        
                        <input type="radio" class="btn-check" name="status" id="status_late" value="late" {{ old('status', $attendance->status) == 'late' ? 'checked' : '' }} autocomplete="off">
                        <label class="btn btn-outline-warning" for="status_late">متأخر</label>
                        
                        <input type="radio" class="btn-check" name="status" id="status_excused" value="excused" {{ old('status', $attendance->status) == 'excused' ? 'checked' : '' }} autocomplete="off">
                        <label class="btn btn-outline-info" for="status_excused">معذور</label>
                        
                        <input type="radio" class="btn-check" name="status" id="status_absent" value="absent" {{ old('status', $attendance->status) == 'absent' ? 'checked' : '' }} autocomplete="off">
                        <label class="btn btn-outline-danger" for="status_absent">غائب</label>
                    </div>
                    @error('status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="teacher_id" class="form-label">المعلم المسؤول <span class="text-danger">*</span></label>
                    <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                        <option value="">-- اختر المعلم --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $attendance->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }} </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">ملاحظات</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-1"></i> حذف السجل
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
                هل أنت متأكد من رغبتك في حذف سجل حضور الطالب "{{ $attendance->student->name }} " بتاريخ {{ $attendance->date->format('Y-m-d') }}؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.attendance.destroy', $attendance->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
