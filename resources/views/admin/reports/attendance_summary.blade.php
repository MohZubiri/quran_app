@extends('layouts.admin')

@section('title', 'تقرير ملخص الحضور')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ url('/admin/reports') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى قائمة التقارير
    </a>
    <button type="button" class="btn btn-sm btn-success" id="exportExcelBtn">
        <i class="fas fa-file-excel me-1"></i> تصدير Excel
    </button>
    <button type="button" class="btn btn-sm btn-danger" id="exportPdfBtn">
        <i class="fas fa-file-pdf me-1"></i> تصدير PDF
    </button>
    <button type="button" class="btn btn-sm btn-primary" id="printReportBtn">
        <i class="fas fa-print me-1"></i> طباعة
    </button>
</div>
@endsection

@section('content')
<!-- فلاتر البحث -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">فلاتر البحث</h5>
    </div>
    <div class="card-body">
        <form action="{{ url('/admin/reports/attendance-summary') }}" method="GET" id="filter-form">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="branch_id" class="form-label">الفرع</label>
                    <select class="form-select" id="branch_id" name="branch_id">
                        <option value="">جميع الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="group_id" class="form-label">الحلقة</label>
                    <select class="form-select" id="group_id" name="group_id">
                        <option value="">جميع الحلقات</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                    <a href="{{ url('/admin/reports/attendance-summary') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- نظرة عامة -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">نظرة عامة على الحضور</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">الحضور الكلي</h6>
                                <p class="card-text display-6">{{ $overview['attendance_rate'] }}%</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">نسبة الحضور</h6>
                                <p class="card-text display-6">{{ $overview['present_rate'] }}%</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">نسبة الغياب</h6>
                                <p class="card-text display-6">{{ $overview['absent_rate'] }}%</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">متوسط الحضور اليومي</h6>
                                <p class="card-text display-6">{{ $overview['daily_average'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">توزيع حالات الحضور</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="attendanceStatusChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">اتجاهات الحضور</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="attendanceTrendChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- تقرير الحضور حسب الحلقة -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">تقرير الحضور حسب الحلقة</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="group-attendance-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الحلقة</th>
                        <th>الفرع</th>
                        <th>عدد الطلاب</th>
                        <th>نسبة الحضور</th>
                        <th>نسبة الغياب</th>
                        <th>نسبة التأخر</th>
                        <th>نسبة الإعفاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupsAttendance as $index => $group)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ url('/admin/groups/' . $group['id']) }}">
                                {{ $group['name'] }}
                            </a>
                        </td>
                        <td>{{ $group['branch_name'] }}</td>
                        <td>{{ $group['students_count'] }}</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $group['present_rate'] }}%;" 
                                     aria-valuenow="{{ $group['present_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $group['present_rate'] }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $group['absent_rate'] }}%;" 
                                     aria-valuenow="{{ $group['absent_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $group['absent_rate'] }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $group['late_rate'] }}%;" 
                                     aria-valuenow="{{ $group['late_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $group['late_rate'] }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $group['excused_rate'] }}%;" 
                                     aria-valuenow="{{ $group['excused_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $group['excused_rate'] }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ url('/admin/groups/' . $group['id']) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('/admin/reports/attendance-by-group?group_id=' . $group['id']) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- تقرير الطلاب الأكثر غيابًا -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">الطلاب الأكثر غيابًا</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الطالب</th>
                        <th>الحلقة</th>
                        <th>عدد أيام الحضور</th>
                        <th>عدد أيام الغياب</th>
                        <th>نسبة الغياب</th>
                        <th>عدد مرات التأخر</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mostAbsentStudents as $index => $student)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ url('/admin/students/' . $student['id']) }}">
                                {{ $student['name'] }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ url('/admin/groups/' . $student['group_id']) }}">
                                {{ $student['group_name'] }}
                            </a>
                        </td>
                        <td>{{ $student['present_days'] }}</td>
                        <td>{{ $student['absent_days'] }}</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $student['absent_rate'] }}%;" 
                                     aria-valuenow="{{ $student['absent_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $student['absent_rate'] }}%
                                </div>
                            </div>
                        </td>
                        <td>{{ $student['late_count'] }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ url('/admin/students/' . $student['id']) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('/admin/reports/student-progress?student_id=' . $student['id']) }}#attendance" class="btn btn-sm btn-primary">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- تقرير الحضور اليومي -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">تقرير الحضور اليومي</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>اليوم</th>
                        <th>حاضر</th>
                        <th>غائب</th>
                        <th>متأخر</th>
                        <th>معفى</th>
                        <th>المجموع</th>
                        <th>نسبة الحضور</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyAttendance as $index => $day)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $day['date'] }}</td>
                        <td>{{ $day['day_name'] }}</td>
                        <td>{{ $day['present_count'] }}</td>
                        <td>{{ $day['absent_count'] }}</td>
                        <td>{{ $day['late_count'] }}</td>
                        <td>{{ $day['excused_count'] }}</td>
                        <td>{{ $day['total'] }}</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ $day['attendance_rate'] >= 90 ? 'bg-success' : ($day['attendance_rate'] >= 70 ? 'bg-info' : ($day['attendance_rate'] >= 50 ? 'bg-warning' : 'bg-danger')) }}" 
                                     role="progressbar" style="width: {{ $day['attendance_rate'] }}%;" 
                                     aria-valuenow="{{ $day['attendance_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $day['attendance_rate'] }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث قوائم الحلقات عند تغيير الفرع
    document.getElementById('branch_id').addEventListener('change', function() {
        const branchId = this.value;
        
        // يمكن إضافة كود Ajax هنا لتحديث قوائم الحلقات بناءً على الفرع المختار
    });
    
    // رسم بياني لتوزيع حالات الحضور
    const statusCtx = document.getElementById('attendanceStatusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['حاضر', 'غائب', 'متأخر', 'معفى'],
            datasets: [{
                data: [
                    {{ $overview['present_count'] }},
                    {{ $overview['absent_count'] }},
                    {{ $overview['late_count'] }},
                    {{ $overview['excused_count'] }}
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(23, 162, 184, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: true
                }
            }
        }
    });

    // رسم بياني لاتجاهات الحضور
    const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
    const trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($dailyAttendance, 'date')) !!},
            datasets: [{
                label: 'نسبة الحضور',
                data: {!! json_encode(array_column($dailyAttendance, 'attendance_rate')) !!},
                borderColor: 'rgba(40, 167, 69, 1)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    rtl: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // تصدير البيانات
    document.getElementById('exportExcelBtn').addEventListener('click', function() {
        window.location.href = "{{ url('/admin/exports/attendance/excel') }}" + window.location.search;
    });

    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        window.location.href = "{{ url('/admin/exports/attendance/pdf') }}" + window.location.search;
    });

    // طباعة التقرير
    document.getElementById('printReportBtn').addEventListener('click', function() {
        window.print();
    });
});
</script>
@endsection
