@extends('layouts.admin')

@section('title', 'إضافة صلاحية جديدة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إضافة صلاحية جديدة</h1>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right ml-1"></i> العودة للصلاحيات
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">بيانات الصلاحية</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">اسم الصلاحية (بالإنجليزية)</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required dir="ltr">
                            <small class="form-text text-muted">مثال: view-students, create-grades (استخدم الحروف الصغيرة والشرطات)</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="display_name">الاسم المعروض (بالعربية)</label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name') }}" required>
                            <small class="form-text text-muted">مثال: عرض الطلاب، إضافة درجات</small>
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="group">المجموعة</label>
                    <select class="form-control @error('group') is-invalid @enderror" id="group" name="group">
                        @foreach($groups as $group)
                            <option value="{{ $group }}" {{ old('group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                        @endforeach
                        <option value="أخرى">أخرى</option>
                    </select>
                    @error('group')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group" id="new-group-container" style="display: none;">
                    <label for="new_group">اسم المجموعة الجديدة</label>
                    <input type="text" class="form-control" id="new_group" name="new_group" value="{{ old('new_group') }}">
                </div>
                
                <div class="form-group">
                    <label for="description">وصف الصلاحية</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save ml-1"></i> حفظ الصلاحية
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const groupSelect = document.getElementById('group');
        const newGroupContainer = document.getElementById('new-group-container');
        
        groupSelect.addEventListener('change', function() {
            if (this.value === 'أخرى') {
                newGroupContainer.style.display = 'block';
            } else {
                newGroupContainer.style.display = 'none';
            }
        });
        
        // Check initial value
        if (groupSelect.value === 'أخرى') {
            newGroupContainer.style.display = 'block';
        }
    });
</script>
@endsection
