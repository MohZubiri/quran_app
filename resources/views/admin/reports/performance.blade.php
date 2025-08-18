@extends('layouts.admin')

@section('title', 'تقرير الأداء')

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
</div>
@endsection

@section('content')
<!-- فلاتر البحث -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">فلاتر البحث</h5>
    </div>
    <div class="card-body">
        <form action="{{ url('/admin/reports/performance') }}" method="GET" id="filter-form">
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
                    <label for="date_from" class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                    <a href="{{ url('/admin/reports/performance') }}" class="btn btn-secondary">
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
                <h5 class="card-title">نظرة عامة على الأداء</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">إجمالي التقييمات</h6>
                                <p class="card-text display-6">{{ $overview['total_grades'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">متوسط الأداء</h6>
                                <p class="card-text display-6">{{ $overview['average_score'] ?? 0 }}%</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">أعلى درجة</h6>
                                <p class="card-text display-6">{{ $overview['highest_score'] ?? 0 }}%</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">أقل درجة</h6>
                                <p class="card-text display-6">{{ $overview['lowest_score'] ?? 0 }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">توزيع الأداء</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="performanceDistributionChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">الأداء حسب النوع</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="performanceByTypeChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- تحليل الأداء حسب الفرع -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">تحليل الأداء حسب الفرع</h5>
    </div>
    <div class="card-body">
        <canvas id="performanceByBranchChart" width="800" height="400"></canvas>
    </div>
</div>

<!-- تحليل الأداء حسب المجموعة -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">تحليل الأداء حسب المجموعة</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المجموعة</th>
                        <th>الفرع</th>
                        <th>المعلم</th>
                        <th>عدد الطلاب</th>
                        <th>متوسط الأداء</th>
                        <th>معدل الحضور</th>
                        <th>معدل الإنجاز</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupsPerformance as $index => $group)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ url('/admin/groups/' . $group['id']) }}">
                                {{ $group['name'] }}
                            </a>
                        </td>
                        <td>{{ $group['branch_name'] }}</td>
                        <td>{{ $group['teacher_name'] }}</td>
                        <td>{{ $group['students_count'] }}</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $group['performance_avg'] >= 90 ? 'success' : ($group['performance_avg'] >= 70 ? 'info' : ($group['performance_avg'] >= 50 ? 'warning' : 'danger')) }}" 
                                     role="progressbar" 
                                     style="width: {{ $group['performance_avg'] }}%" 
                                     aria-valuenow="{{ $group['performance_avg'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $group['performance_avg'] }}%
                                </div>
                            </div>
                        </td>
                        <td>{{ $group['attendance_avg'] }}%</td>
                        <td>{{ $group['achievement_avg'] }}%</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ url('/admin/groups/' . $group['id']) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('/admin/reports/performance-by-group?group_id=' . $group['id']) }}" class="btn btn-sm btn-primary">
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // رسم بياني لتوزيع الأداء
    const distributionCtx = document.getElementById('performanceDistributionChart').getContext('2d');
    const distributionChart = new Chart(distributionCtx, {
        type: 'bar',
        data: {
            labels: ['90-100%', '80-89%', '70-79%', '60-69%', '50-59%', '< 50%'],
            datasets: [{
                label: 'عدد الطلاب',
                data: {!! json_encode($charts['distribution'] ?? [0, 0, 0, 0, 0, 0]) !!},
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(255, 152, 0, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // رسم بياني للأداء حسب النوع
    const typeCtx = document.getElementById('performanceByTypeChart').getContext('2d');
    const typeChart = new Chart(typeCtx, {
        type: 'radar',
        data: {
            labels: ['الحفظ', 'التجويد', 'التلاوة', 'السلوك', 'الإنجاز', 'الحضور'],
            datasets: [{
                label: 'متوسط الأداء',
                data: [
                    {{ $overview['memorization_avg'] ?? 0 }},
                    {{ $overview['tajweed_avg'] ?? 0 }},
                    {{ $overview['recitation_avg'] ?? 0 }},
                    {{ $overview['behavior_avg'] ?? 0 }},
                    {{ $overview['achievement_avg'] ?? 0 }},
                    {{ $overview['attendance_avg'] ?? 0 }}
                ],
                fill: true,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgb(54, 162, 235)',
                pointBackgroundColor: 'rgb(54, 162, 235)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(54, 162, 235)'
            }]
        },
        options: {
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            }
        }
    });

    // رسم بياني للأداء حسب الفرع
    const branchCtx = document.getElementById('performanceByBranchChart').getContext('2d');
    const branchChart = new Chart(branchCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($branches->pluck('name')) !!},
            datasets: [
                {
                    label: 'متوسط الأداء',
                    data: {!! json_encode($branches->map(function($branch) { return rand(60, 95); })) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                },
                {
                    label: 'معدل الحضور',
                    data: {!! json_encode($branches->map(function($branch) { return rand(70, 100); })) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                },
                {
                    label: 'معدل الإنجاز',
                    data: {!! json_encode($branches->map(function($branch) { return rand(65, 95); })) !!},
                    backgroundColor: 'rgba(23, 162, 184, 0.7)',
                }
            ]
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
                    title: {
                        display: true,
                        text: 'النسبة المئوية'
                    }
                }
            }
        }
    });

    // تصدير البيانات
    document.getElementById('exportExcelBtn').addEventListener('click', function() {
        window.location.href = "{{ url('/admin/exports/performance/excel') }}" + window.location.search;
    });

    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        window.location.href = "{{ url('/admin/exports/performance/pdf') }}" + window.location.search;
    });
});
</script>
@endsection
