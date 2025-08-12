<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد - نظام إدارة حلقات تحفيظ القرآن</title>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .register-container {
            max-width: 550px;
            width: 100%;
            padding: 20px;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: linear-gradient(135deg, #36b9cc 0%, #1a8a98 100%);
            color: white;
            text-align: center;
            border-radius: 10px 10px 0 0 !important;
            padding: 20px;
        }
        
        .logo {
            max-width: 80px;
            margin-bottom: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #36b9cc 0%, #1a8a98 100%);
            border: none;
            padding: 10px 20px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #1a8a98 0%, #0f6570 100%);
        }
        
        .form-control {
            padding: 12px;
            border-radius: 5px;
        }
        
        .register-footer {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
        }
        
        .role-selector {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .role-card {
            flex: 1;
            text-align: center;
            padding: 15px;
            margin: 0 5px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #e9ecef;
        }
        
        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .role-card.selected {
            border-color: #36b9cc;
            background-color: rgba(54, 185, 204, 0.1);
        }
        
        .role-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #36b9cc;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="card">
            <div class="card-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
                <h3>نظام إدارة حلقات تحفيظ القرآن</h3>
                <p class="mb-0">إنشاء حساب جديد</p>
            </div>
            
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    
                    <!-- Role Selection -->
                    <div class="mb-4">
                        <label class="form-label">اختر نوع الحساب</label>
                        <div class="role-selector">
                            <div class="role-card" data-role="admin" onclick="selectRole('admin')">
                                <div class="role-icon">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <h5>مدير</h5>
                            </div>
                            <div class="role-card" data-role="teacher" onclick="selectRole('teacher')">
                                <div class="role-icon">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <h5>معلم</h5>
                            </div>
                            <div class="role-card" data-role="student" onclick="selectRole('student')">
                                <div class="role-icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <h5>طالب</h5>
                            </div>
                        </div>
                        <input type="hidden" name="role" id="role" value="{{ old('role', 'student') }}">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">الاسم الكامل</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3 phone-field">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                            </div>
                            <small class="text-muted student-note">مطلوب للطلاب للدخول بواسطة رقم الهاتف</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>إنشاء حساب
                        </button>
                    </div>
                    
                    <div class="text-center mt-3">
                        <p>لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a></p>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="register-footer">
            <p>&copy; {{ date('Y') }} نظام إدارة حلقات تحفيظ القرآن. جميع الحقوق محفوظة.</p>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Role selection functionality
        function selectRole(role) {
            // Update hidden input
            document.getElementById('role').value = role;
            
            // Update UI
            document.querySelectorAll('.role-card').forEach(card => {
                if (card.getAttribute('data-role') === role) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            });
            
            // Show/hide phone field note for students
            const studentNote = document.querySelector('.student-note');
            if (role === 'student') {
                studentNote.style.display = 'block';
            } else {
                studentNote.style.display = 'none';
            }
        }
        
        // Initialize role selection
        document.addEventListener('DOMContentLoaded', function() {
            const initialRole = document.getElementById('role').value || 'student';
            selectRole(initialRole);
        });
    </script>
</body>
</html>
