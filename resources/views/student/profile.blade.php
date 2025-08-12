@extends('layouts.student')

@section('title', 'الملف الشخصي')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">الملف الشخصي</h1>
    </div>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-gradient-primary">
                <h5 class="mb-0">المعلومات الشخصية</h5>
            </div>
            <div class="card-body text-center">
                <div class="avatar-circle mb-4">
                    <i class="fas fa-user"></i>
                </div>
                
                <h3>{{ $student->name }}</h3>
                <p class="text-muted">{{ $student->email }}</p>
                
                <div class="d-flex justify-content-center mt-3">
                    <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fas fa-key me-1"></i> تغيير كلمة المرور
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Student Details -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">تفاصيل الطالب</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">العمر</h6>
                        <p>{{ $student->age }} سنة</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">تاريخ الميلاد</h6>
                        <p>{{ $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : 'غير محدد' }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">الجنس</h6>
                        <p>{{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">رقم الهاتف</h6>
                        <p>{{ $student->phone ?: 'غير محدد' }}</p>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <h6 class="text-muted">العنوان</h6>
                        <p>{{ $student->address ?: 'غير محدد' }}</p>
                    </div>
                   
                   
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Academic Information -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">المعلومات الدراسية</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">الفرع</h6>
                        <p>{{ $student->branch->name ?? 'غير محدد' }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">المجموعة</h6>
                        <p>{{ $student->group->name ?? 'غير محدد' }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">تاريخ الانضمام</h6>
                        <p>{{ $student->created_at ? $student->created_at->format('Y-m-d') : 'غير محدد' }}</p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">المستوى الحالي</h6>
                        <p>{{ $student->current_level ?: 'غير محدد' }}</p>
                    </div>
                    
                  
                </div>
            </div>
        </div>
    </div>
    
    <!-- Account Information -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">معلومات الحساب</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <h6 class="text-muted">اسم المستخدم</h6>
                        <p>{{ $user->name }}</p>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <h6 class="text-muted">البريد الإلكتروني</h6>
                        <p>{{ $user->email }}</p>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <h6 class="text-muted">تاريخ إنشاء الحساب</h6>
                        <p>{{ $user->created_at->format('Y-m-d') }}</p>
                    </div>
                    
                    <div class="col-md-12">
                        <h6 class="text-muted">آخر تسجيل دخول</h6>
                        <p>{{ $user->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">تغيير كلمة المرور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">كلمة المرور الحالية</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
