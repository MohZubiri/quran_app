@extends('layouts.admin')

@section('title', 'إضافة خطة دراسية جديدة')

@section('actions')
<a href="{{ route('admin.study_plans.index') }}" class="btn btn-sm btn-secondary">
    <i class="fas fa-arrow-right me-1"></i> العودة إلى قائمة الخطط الدراسية
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">إضافة خطة دراسية جديدة</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.study_plans.store') }}" method="POST">
            @csrf

            <div class="mb-3 row">
                <div class="col-md-6">
                    <label class="form-label">رقم الخطة <span class="text-danger">*</span></label>
                    <input type="text" name="plan_number" value="{{ old('plan_number') }}" class="form-control @error('plan_number') is-invalid @enderror" required>
                    @error('plan_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">المجموعة <span class="text-danger">*</span></label>
                    <select name="group_number" class="form-select @error('group_number') is-invalid @enderror" required>
                        <option value="">-- اختر المجموعة --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group_number') == $group->id ? 'selected' : '' }}>
                                {{ $group->id }} - {{ $group->name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('group_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-md-6">
                    <label class="form-label">عدد الدروس او الصفحات <span class="text-danger">*</span></label>
                    <input type="number" name="lessons_count" value="{{ old('lessons_count') }}" class="form-control @error('lessons_count') is-invalid @enderror" required>
                    @error('lessons_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">أقل أداء  <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="min_performance" value="{{ old('min_performance') }}" class="form-control @error('min_performance') is-invalid @enderror" required>
                    @error('min_performance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>فعال</option>
                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>غير فعال</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> حفظ الخطة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
