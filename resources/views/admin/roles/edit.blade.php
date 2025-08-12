@extends('layouts.admin')

@section('title', 'تعديل الدور')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل الدور: {{ $role->display_name }}</h1>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right ml-1"></i> العودة للأدوار
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">بيانات الدور</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">اسم الدور (بالإنجليزية)</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required dir="ltr" {{ in_array($role->name, ['admin', 'teacher', 'student']) ? 'readonly' : '' }}>
                            <small class="form-text text-muted">مثال: branch-manager, supervisor (استخدم الحروف الصغيرة والشرطات)</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="display_name">الاسم المعروض (بالعربية)</label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name', $role->display_name) }}" required>
                            <small class="form-text text-muted">مثال: مدير فرع، مشرف</small>
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">وصف الدور</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>الصلاحيات</label>
                    <div class="card">
                        <div class="card-body">
                            @forelse($permissionsByGroup as $group => $permissions)
                                <div class="mb-3">
                                    <h6 class="border-right-primary pr-2 mb-2">{{ $group }}</h6>
                                    <div class="row">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-4 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" 
                                                           id="permission_{{ $permission->id }}" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}"
                                                           {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                                        {{ $permission->display_name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">لا توجد صلاحيات مضافة حتى الآن</p>
                            @endforelse
                        </div>
                    </div>
                    @error('permissions')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save ml-1"></i> حفظ التغييرات
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add select all checkbox for each permission group
        const permissionGroups = document.querySelectorAll('.mb-3');
        
        permissionGroups.forEach(function(group, index) {
            const checkboxes = group.querySelectorAll('input[type="checkbox"]');
            const heading = group.querySelector('h6');
            
            // Create select all checkbox
            const selectAllDiv = document.createElement('div');
            selectAllDiv.className = 'custom-control custom-checkbox d-inline-block ml-2';
            
            const selectAllInput = document.createElement('input');
            selectAllInput.type = 'checkbox';
            selectAllInput.className = 'custom-control-input';
            selectAllInput.id = `select_all_${index}`;
            
            const selectAllLabel = document.createElement('label');
            selectAllLabel.className = 'custom-control-label';
            selectAllLabel.htmlFor = `select_all_${index}`;
            selectAllLabel.textContent = 'تحديد الكل';
            
            selectAllDiv.appendChild(selectAllInput);
            selectAllDiv.appendChild(selectAllLabel);
            
            heading.appendChild(selectAllDiv);
            
            // Add event listener to select all checkbox
            selectAllInput.addEventListener('change', function() {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = selectAllInput.checked;
                });
            });
            
            // Update select all checkbox when individual checkboxes change
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(checkboxes).every(function(cb) {
                        return cb.checked;
                    });
                    
                    const anyChecked = Array.from(checkboxes).some(function(cb) {
                        return cb.checked;
                    });
                    
                    selectAllInput.checked = allChecked;
                    selectAllInput.indeterminate = anyChecked && !allChecked;
                });
            });
            
            // Initialize select all checkbox state
            const allChecked = Array.from(checkboxes).every(function(cb) {
                return cb.checked;
            });
            
            const anyChecked = Array.from(checkboxes).some(function(cb) {
                return cb.checked;
            });
            
            selectAllInput.checked = allChecked;
            selectAllInput.indeterminate = anyChecked && !allChecked;
        });
    });
</script>
@endsection
