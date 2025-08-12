@extends('layouts.admin')

@section('title', 'إدارة صلاحيات المستخدم')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة صلاحيات المستخدم: {{ $user->name }}</h1>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right ml-1"></i> العودة
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المستخدم</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">الاسم:</h6>
                        <p>{{ $user->name }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">البريد الإلكتروني:</h6>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">رقم الهاتف:</h6>
                        <p>{{ $user->phone ?: 'غير متوفر' }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">نوع المستخدم:</h6>
                        <p>
                            @if($user->isAdmin())
                                <span class="badge badge-danger">مدير النظام</span>
                            @elseif($user->isTeacher())
                                <span class="badge badge-primary">معلم</span>
                            @elseif($user->isStudent())
                                <span class="badge badge-success">طالب</span>
                            @else
                                <span class="badge badge-secondary">غير محدد</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الأدوار</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update-roles', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <div class="row">
                                @foreach($roles as $role)
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" 
                                                   id="role_{{ $role->id }}" 
                                                   name="roles[]" 
                                                   value="{{ $role->id }}"
                                                   {{ in_array($role->id, $userRoles) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="role_{{ $role->id }}">
                                                {{ $role->display_name }}
                                                <small class="d-block text-muted">{{ $role->description }}</small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save ml-1"></i> حفظ الأدوار
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الصلاحيات المباشرة</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle ml-1"></i>
                        الصلاحيات المباشرة هي صلاحيات إضافية تُمنح للمستخدم بشكل مباشر، بغض النظر عن الأدوار التي يمتلكها.
                        <hr>
                        <strong>ملاحظة:</strong> المستخدم يمتلك بالفعل الصلاحيات التالية من خلال الأدوار المسندة إليه:
                        <div class="mt-2">
                            @forelse($rolePermissions as $permissionId)
                                <span class="badge badge-secondary p-2 ml-1 mb-1">
                                    {{ \App\Models\Permission::find($permissionId)->display_name }}
                                </span>
                            @empty
                                <span class="text-muted">لا توجد صلاحيات من خلال الأدوار</span>
                            @endforelse
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.users.update-permissions', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
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
                                                           {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}
                                                           {{ in_array($permission->id, $rolePermissions) ? 'disabled' : '' }}>
                                                    <label class="custom-control-label {{ in_array($permission->id, $rolePermissions) ? 'text-muted' : '' }}" for="permission_{{ $permission->id }}">
                                                        {{ $permission->display_name }}
                                                        @if(in_array($permission->id, $rolePermissions))
                                                            <small class="d-block text-muted">(متوفرة من خلال الأدوار)</small>
                                                        @endif
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
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save ml-1"></i> حفظ الصلاحيات المباشرة
                        </button>
                    </form>
                </div>
            </div>
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
            const checkboxes = group.querySelectorAll('input[type="checkbox"]:not([disabled])');
            
            // Skip if no enabled checkboxes
            if (checkboxes.length === 0) {
                return;
            }
            
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
