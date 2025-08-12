<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة حلقات تحفيظ القرآن</title>
    
    <!-- البيانات الوصفية -->
    <meta name="description" content="نظام إدارة حلقات تحفيظ القرآن الكريم - تسجيل الدخول للمدرسين والإداريين">
    <meta name="keywords" content="تحفيظ القرآن, نظام إدارة, تعليم القرآن, حلقات القرآن">
    <meta name="author" content="نظام إدارة حلقات تحفيظ القرآن">
    <meta name="theme-color" content="#198754">
    <link rel="icon" href="{{ asset('storage/images/quran-icon.png') }}">
    
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
            margin: 0;
            padding: 0;
        }
        
        .login-container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
            border-radius: 15px;
            overflow: hidden;
            background-color: #fff;
            height: auto;
            min-height: 600px;
            margin: 15px;
        }
        
        .mosque-image {
            flex: 1;
            background-image: url('https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?q=80&w=1000');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .mosque-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.7) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .mosque-overlay h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }
        
        .mosque-overlay p {
            font-size: 1.2rem;
            text-align: center;
            max-width: 80%;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        .login-form-container {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            max-width: 120px;
            margin-bottom: 15px;
        }
        
        .card {
            border: none;
            box-shadow: none;
        }
        
        .card-header {
            background: none;
            color: #333;
            text-align: center;
            border-radius: 0 !important;
            padding: 0;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #224abe 0%, #1a3a8a 100%);
        }
        
        .form-control {
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
        }
        
        .btn-link {
            color: #4e73df;
            text-decoration: none;
            font-weight: 600;
        }
        
        .btn-link:hover {
            color: #224abe;
            text-decoration: underline;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-logo img {
            max-width: 120px;
            margin-bottom: 1rem;
        }
        
        /* Enhanced Mobile Responsiveness */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                height: auto;
                max-width: 500px;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
            }
            
            body {
                background-color: #fff;
            }
            
            .mosque-image {
                height: 180px;
            }
            
            .login-form-container {
                padding: 20px 25px 40px;
            }
            
            .mosque-overlay h1 {
                font-size: 1.8rem;
            }
            
            .mosque-overlay p {
                font-size: 1rem;
                max-width: 90%;
            }
            
            .logo-container {
                margin-bottom: 20px;
            }
            
            .logo {
                display: none;
            }
            
            .logo-container h3 {
                font-size: 1.5rem;
                margin-bottom: 5px;
            }
            
            .logo-container p {
                font-size: 0.9rem;
                margin-bottom: 15px;
            }
            
            .form-label {
                text-align: right;
                display: block;
                margin-bottom: 8px;
            }
            
            .form-control {
                padding: 10px;
            }
            
            .btn-primary {
                padding: 12px 15px;
                margin-top: 15px;
            }
            
            .login-footer {
                margin-top: 30px;
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 576px) {
            .login-container {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
            }
            
            .mosque-image {
                height: 150px;
            }
            
            .mosque-overlay h1 {
                font-size: 1.5rem;
                margin-bottom: 10px;
            }
            
            .mosque-overlay p {
                font-size: 0.9rem;
                max-width: 95%;
            }
        }
        
        /* Fix for very small screens */
        @media (max-width: 320px) {
            .login-form-container {
                padding: 15px 15px 30px;
            }
            
            .mosque-image {
                height: 120px;
            }
            
            .mosque-overlay h1 {
                font-size: 1.2rem;
            }
            
            .mosque-overlay p {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="mosque-image">
            <div class="mosque-overlay">
                <h1>نظام إدارة حلقات تحفيظ القرآن</h1>
                <p>منصة متكاملة لإدارة حلقات تحفيظ القرآن الكريم ومتابعة الطلاب والمعلمين</p>
            </div>
        </div>
        
        <div class="login-form-container">
            <div class="login-logo">
                <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" class="logo">
                <h3>تسجيل الدخول</h3>
                <p class="text-muted">أدخل بيانات حسابك للوصول إلى لوحة التحكم</p>
            </div>
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="login" class="form-label">{{ __('البريد الإلكتروني أو رقم الهاتف') }}</label>
                    <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" required autofocus>
                    @error('login')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('كلمة المرور') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('تذكرني') }}
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">
                        {{ __('تسجيل الدخول') }}
                    </button>
                </div>

                <div class="text-center">
                    @if (Route::has('password.request'))
                        <a class="btn-link" href="{{ route('password.request') }}">
                            {{ __('نسيت كلمة المرور؟') }}
                        </a>
                    @endif
                </div>
            </form>
            
            <div class="login-footer">
                <p>&copy; {{ date('Y') }} نظام إدارة حلقات تحفيظ القرآن. جميع الحقوق محفوظة <a href="https://softnube.site" target="_blank" class="text-decoration-none">لشركة softnube</a>.</p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
