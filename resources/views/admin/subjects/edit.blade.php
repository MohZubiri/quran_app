@extends('layouts.admin')

@section('title', 'تعديل المادة الدراسية')

@section('actions')
<a href="{{ route('admin.subjects.index') }}" class="btn btn-sm btn-secondary">
    <i class="fas fa-arrow-right me-1"></i> العودة إلى قائمة المواد الدراسية
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">تعديل المادة الدراسية: {{ $subject->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.subjects.update', $subject->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">اسم المادة <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $subject->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="branch_id" class="form-label">الفرع <span class="text-danger">*</span></label>
                    <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                        <option value="">-- اختر الفرع --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $subject->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">يمكنك فقط اختيار الفروع التي تنتمي إلى حسابك.</small>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">وصف المادة</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $subject->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="level" class="form-label">المستوى</label>
                    <select class="form-select @error('level') is-invalid @enderror" id="level" name="level">
                        <option value="">-- اختر المستوى --</option>
                        <option value="beginner" {{ old('level', $subject->level) == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                        <option value="intermediate" {{ old('level', $subject->level) == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                        <option value="advanced" {{ old('level', $subject->level) == 'advanced' ? 'selected' : '' }}>متقدم</option>
                    </select>
                    @error('level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status', $subject->status) == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ old('status', $subject->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
          
            
            
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
