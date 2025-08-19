<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - نظام تعليم القرآن</title>
     <!-- البيانات الوصفية -->
     <meta name="description" content="نظام إدارة حلقات تحفيظ القرآن الكريم - تسجيل الدخول للمدرسين والإداريين">
    <meta name="keywords" content="تحفيظ القرآن, نظام إدارة, تعليم القرآن, حلقات القرآن">
    <meta name="author" content="نظام إدارة حلقات تحفيظ القرآن">
    <meta name="theme-color" content="#198754">
    <link rel="icon" href="{{ asset('storage/images/quran-icon.png') }}">

    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Fonts - Cairo (Arabic) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: #fff;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }

        .main-content {
            padding: 20px;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .border-left-primary {
            border-right: 4px solid #4e73df !important;
            border-left: none !important;
        }

        .border-left-success {
            border-right: 4px solid #1cc88a !important;
            border-left: none !important;
        }

        .border-left-info {
            border-right: 4px solid #36b9cc !important;
            border-left: none !important;
        }

        .border-left-warning {
            border-right: 4px solid #f6c23e !important;
            border-left: none !important;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
    </style>

    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="pt-3 position-sticky">
                    <div class="mb-4 text-center">
                        <h4 class="text-white">نظام تعليم القرآن</h4>
                    </div>

                    <ul class="nav flex-column">
                        @if(auth()->user()->can('view-dashboard'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                لوحة التحكم
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('view-branches'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.branches*') ? 'active' : '' }}" href="{{ route('admin.branches.index') }}">
                                <i class="fas fa-building me-2"></i>
                                الفروع
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('view-students') && auth()->user()->checkGroup())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
                                <i class="fas fa-user-graduate me-2"></i>
                                الطلاب
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('view-teachers'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.teachers*') ? 'active' : '' }}" href="{{ route('admin.teachers.index') }}">
                                <i class="fas fa-chalkboard-teacher me-2"></i>
                                المعلمين
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('view-subjects') )
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}" href="{{ route('admin.subjects.index') }}">
                                <i class="fas fa-book me-2"></i>
                                المواد الدراسية
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('view-groups')&& auth()->user()->checkGroup())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.groups*') ? 'active' : '' }}" href="{{ route('admin.groups.index') }}">
                                <i class="fas fa-users me-2"></i>
                                الحلقات
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('view-enrollments')&& auth()->user()->checkGroup())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.enrollments*') ? 'active' : '' }}" href="{{ route('admin.enrollments.index') }}">
                                <i class="fas fa-user-plus me-2"></i>
                                التسجيلات
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('view-attendance')&& auth()->user()->checkGroup())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
                                <i class="fas fa-clipboard-check me-2"></i>
                                الحضور
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->can('view-plans'))

                        @endif

                        @if(auth()->user()->can('view-grades')&& auth()->user()->checkGroup())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.grades*') ? 'active' : '' }}" href="{{ route('admin.grades.index') }}">
                                <i class="fas fa-chart-line me-2"></i>
                                الدرجات
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('view-progress-logs'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.progress-logs*') ? 'active' : '' }}" href="{{ route('admin.progress-logs.index') }}">
                                <i class="fas fa-tasks me-2"></i>
                                سجلات التقدم
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('view-reports'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                                <i class="fas fa-chart-bar me-2"></i>
                                التقارير والإحصائيات
                            </a>
                        </li>
                        @endif

                        <!-- إدارة المستخدمين -->
                        @if(auth()->user()->can('view-users'))
                        <li class="mt-3 nav-item">
                            <h6 class="px-3 mb-1 sidebar-heading d-flex justify-content-between align-items-center text-muted">
                                <span>إدارة المستخدمين</span>
                            </h6>
                        </li>

                        @if(auth()->user()->can('view-users'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users me-2"></i>
                                المستخدمين
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('view-roles'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                                <i class="fas fa-user-tag me-2"></i>
                                الأدوار
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->can('view-permissions'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.permissions*') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">
                                <i class="fas fa-key me-2"></i>
                                الصلاحيات
                            </a>
                        </li>
                        @endif
                        @endif
                    </ul>

                    <hr class="text-white-50">

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    تسجيل الخروج
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top Navbar -->
                <nav class="mb-4 rounded shadow-sm navbar navbar-expand-lg navbar-light bg-light">
                    <div class="container-fluid">
                        <button class="navbar-toggler d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="d-flex align-items-center">
                            <div class="dropdown">
                                <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=random" alt="{{ Auth::user()->name }}" width="32" height="32" class="rounded-circle me-2">
                                    <span>{{ Auth::user()->name }}</span>
                                </a>
                                <ul class="shadow dropdown-menu dropdown-menu-end text-small" aria-labelledby="dropdownUser">
                                    <li><a class="dropdown-item" href="#">الملف الشخصي</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                                تسجيل الخروج
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Flash Messages -->
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

                <!-- Page Title -->
                <div class="flex-wrap pt-3 pb-2 mb-3 d-flex justify-content-between flex-md-nowrap align-items-center border-bottom">
                    <h1 class="h2">@yield('title')</h1>
                    @yield('actions')
                </div>

                <!-- Main Content -->
                @yield('content')

                <!-- Footer -->
                <footer class="mt-5 mb-3 text-center text-muted">
                    <p>&copy; {{ date('Y') }} نظام إدارة حلقات تحفيظ القرآن. جميع الحقوق محفوظة <a href="https://softnube.site" target="_blank" class="text-decoration-none">لشركة softnube</a>.</p>
                </footer>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>
