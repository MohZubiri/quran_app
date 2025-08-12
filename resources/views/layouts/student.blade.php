<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title')</title>
     <!-- البيانات الوصفية -->
     <meta name="description" content="نظام إدارة حلقات تحفيظ القرآن الكريم - تسجيل الدخول للمدرسين والإداريين">
    <meta name="keywords" content="تحفيظ القرآن, نظام إدارة, تعليم القرآن, حلقات القرآن">
    <meta name="author" content="نظام إدارة حلقات تحفيظ القرآن">
    <meta name="theme-color" content="#198754">
    <link rel="icon" href="{{ asset('storage/images/quran-icon.png') }}">
    
    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Cairo Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 5px;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .main-content {
            padding: 20px;
        }
        
        .card {
            border-right: 4px solid #28a745;
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
            color: white;
        }
        
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
            color: white;
        }
        
        .progress {
            height: 10px;
            margin-top: 5px;
        }
        
        .progress-bar-memorization {
            background-color: #4e73df;
        }
        
        .progress-bar-tajweed {
            background-color: #1cc88a;
        }
        
        .progress-bar-recitation {
            background-color: #36b9cc;
        }
        
        .progress-bar-behavior {
            background-color: #f6c23e;
        }
        
        .grade-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }
        
        .grade-excellent {
            background-color: #1cc88a;
        }
        
        .grade-good {
            background-color: #4e73df;
        }
        
        .grade-average {
            background-color: #f6c23e;
        }
        
        .grade-poor {
            background-color: #e74a3b;
        }
        
        .avatar-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #4e73df;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            margin: 0 auto 20px;
        }
        
        .default-logo {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            background-color: #4e73df;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            margin: 0 auto;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar d-none d-md-block">
                <div class="text-center mb-4">
                    @php
                        $branch = App\Models\Branch::where('id', auth()->user()->branch_id)->first();
                    @endphp
                    @if($branch && $branch->logo)
                        <div style="width: 100px; height: 100px; margin: 0 auto;" class="mb-3">
                            <img src="{{ asset('storage/' . $branch->logo) }}" 
                                alt="{{ $branch->name }}" 
                                class="img-fluid" 
                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                        </div>
                    @else
                        <div class="default-logo mb-3">
                            <i class="fas fa-mosque"></i>
                        </div>
                    @endif
                    <h4 class="text-white">{{ $branch->name }}</h4>
                    <p class="text-white-50">لوحة الطالب</p>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> الرئيسية
                        </a>
                    </li>
                 
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}" href="{{ route('student.profile') }}">
                            <i class="fas fa-user me-2"></i> الملف الشخصي
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link text-danger border-0 bg-transparent w-100 text-end">
                                <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 ms-sm-auto px-md-4 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 shadow-sm">
                    <div class="container-fluid">
                        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="d-flex align-items-center">
                            <span class="me-2">مرحبًا، {{ Auth::user()->name }}</span>
                            <div class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle fa-lg"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('student.profile') }}">الملف الشخصي</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">تسجيل الخروج</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Mobile Sidebar (Visible only on small screens) -->
                <div class="collapse d-md-none" id="sidebarMenu">
                    <ul class="nav flex-column mb-4">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> الرئيسية
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.grades') ? 'active' : '' }}" href="{{ route('student.grades') }}">
                                <i class="fas fa-star me-2"></i> الدرجات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.attendance') ? 'active' : '' }}" href="{{ route('student.attendance') }}">
                                <i class="fas fa-calendar-check me-2"></i> الحضور
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.progress') ? 'active' : '' }}" href="{{ route('student.progress') }}">
                                <i class="fas fa-chart-line me-2"></i> سجل التقدم
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}" href="{{ route('student.profile') }}">
                                <i class="fas fa-user me-2"></i> الملف الشخصي
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Page Content -->
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                    
                    <!-- Footer -->
                    <footer class="mt-5 mb-3 text-center text-muted">
                        <p>&copy; {{ date('Y') }} نظام إدارة حلقات تحفيظ القرآن. جميع الحقوق محفوظة <a href="https://softnube.site" target="_blank" class="text-decoration-none">لشركة softnube</a>.</p>
                    </footer>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @yield('scripts')
</body>
</html>
