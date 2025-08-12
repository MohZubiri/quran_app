@extends('layouts.admin')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">لوحة التقارير والإحصائيات</h5>
            </div>
            <div class="card-body">
                <p class="card-text">مرحبًا بك في نظام التقارير والإحصائيات. اختر نوع التقرير الذي ترغب في عرضه من القائمة أدناه.</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-graduate me-2"></i> تقارير الطلاب
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">تقارير مفصلة عن أداء الطلاب، مستوى التقدم، والإحصائيات العامة.</p>
                <div class="list-group mt-3">
                    <a href="{{ route('admin.reports.students') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-line me-2"></i> أداء الطلاب
                    </a>
                    <a href="{{ route('admin.reports.student_progress') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-tasks me-2"></i> تقدم الطلاب
                    </a>
                    <a href="{{ route('admin.reports.student_comparison') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-balance-scale me-2"></i> مقارنة بين الطلاب
                    </a>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reports.students') }}" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-arrow-circle-left me-1"></i> عرض جميع تقارير الطلاب
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-check me-2"></i> تقارير الحضور
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">إحصائيات وتقارير تفصيلية عن حضور الطلاب والمعلمين في الحلقات.</p>
                <div class="list-group mt-3">
                    <a href="{{ route('admin.reports.attendance_summary') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-pie me-2"></i> ملخص الحضور
                    </a>
                    <a href="{{ route('admin.reports.attendance_by_group') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-users me-2"></i> الحضور حسب الحلقة
                    </a>
                    <a href="{{ route('admin.reports.attendance_trends') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-line me-2"></i> اتجاهات الحضور
                    </a>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reports.attendance') }}" class="btn btn-success btn-sm w-100">
                    <i class="fas fa-arrow-circle-left me-1"></i> عرض جميع تقارير الحضور
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-award me-2"></i> تقارير التقييمات
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">تقارير وإحصائيات عن تقييمات الطلاب ومستويات الأداء.</p>
                <div class="list-group mt-3">
                    <a href="{{ route('admin.reports.grades_summary') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-bar me-2"></i> ملخص التقييمات
                    </a>
                    <a href="{{ route('admin.reports.grades_by_subject') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-book me-2"></i> التقييمات حسب المادة
                    </a>
                    <a href="{{ route('admin.reports.grades_by_teacher') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-chalkboard-teacher me-2"></i> التقييمات حسب المعلم
                    </a>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reports.grades') }}" class="btn btn-info btn-sm w-100">
                    <i class="fas fa-arrow-circle-left me-1"></i> عرض جميع تقارير التقييمات
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i> تقارير الأداء
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">تقارير تحليلية للأداء العام في النظام، مع مقارنات ومؤشرات.</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="list-group mt-3">
                            <a href="{{ route('admin.reports.performance_by_branch') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-code-branch me-2"></i> الأداء حسب الفرع
                            </a>
                            <a href="{{ route('admin.reports.performance_by_group') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-users me-2"></i> الأداء حسب الحلقة
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="list-group mt-3">
                            <a href="{{ route('admin.reports.performance_trends') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-line me-2"></i> اتجاهات الأداء
                            </a>
                            <a href="{{ route('admin.reports.performance_comparisons') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-balance-scale me-2"></i> مقارنات الأداء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.reports.performance') }}" class="btn btn-warning btn-sm w-100">
                    <i class="fas fa-arrow-circle-left me-1"></i> عرض جميع تقارير الأداء
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-export me-2"></i> تصدير التقارير
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">تصدير البيانات والتقارير بصيغ مختلفة للاستخدام الخارجي.</p>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mt-3">تصدير بيانات الطلاب</h6>
                        <div class="list-group mt-2">
                            <a href="{{ route('admin.exports.students', ['format' => 'excel']) }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-excel me-2 text-success"></i> تصدير بصيغة Excel
                            </a>
                            <a href="{{ route('admin.exports.students', ['format' => 'pdf']) }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-pdf me-2 text-danger"></i> تصدير بصيغة PDF
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mt-3">تصدير بيانات التقييمات</h6>
                        <div class="list-group mt-2">
                            <a href="{{ route('admin.exports.grades', ['format' => 'excel']) }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-excel me-2 text-success"></i> تصدير بصيغة Excel
                            </a>
                            <a href="{{ route('admin.exports.grades', ['format' => 'pdf']) }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-pdf me-2 text-danger"></i> تصدير بصيغة PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.exports.index') }}" class="btn btn-danger btn-sm w-100">
                    <i class="fas fa-arrow-circle-left me-1"></i> عرض جميع خيارات التصدير
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Analytics -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">نظرة عامة على الإحصائيات</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">إجمالي الطلاب</h6>
                                <p class="card-text display-6">{{ $stats['total_students'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">نسبة الحضور</h6>
                                <p class="card-text display-6">{{ $stats['attendance_rate'] }}%</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">متوسط التقييمات</h6>
                                <p class="card-text display-6">{{ $stats['average_grade'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">عدد الحلقات</h6>
                                <p class="card-text display-6">{{ $stats['total_groups'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">توزيع الطلاب حسب الفرع</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="branchDistributionChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
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

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">توزيع التقييمات</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="gradesDistributionChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">تقدم الطلاب في الشهر الحالي</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyProgressChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // توزيع الطلاب حسب الفرع
    const branchCtx = document.getElementById('branchDistributionChart').getContext('2d');
    const branchChart = new Chart(branchCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($charts['branches']['labels']) !!},
            datasets: [{
                data: {!! json_encode($charts['branches']['data']) !!},
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
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

    // اتجاهات الحضور
    const attendanceCtx = document.getElementById('attendanceTrendChart').getContext('2d');
    const attendanceChart = new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($charts['attendance']['labels']) !!},
            datasets: [{
                label: 'نسبة الحضور',
                data: {!! json_encode($charts['attendance']['data']) !!},
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

    // توزيع التقييمات
    const gradesCtx = document.getElementById('gradesDistributionChart').getContext('2d');
    const gradesChart = new Chart(gradesCtx, {
        type: 'bar',
        data: {
            labels: ['ممتاز (90-100)', 'جيد جدًا (80-89)', 'جيد (70-79)', 'مقبول (60-69)', 'ضعيف (أقل من 60)'],
            datasets: [{
                label: 'عدد الطلاب',
                data: {!! json_encode($charts['grades']['data']) !!},
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // تقدم الطلاب في الشهر الحالي
    const progressCtx = document.getElementById('monthlyProgressChart').getContext('2d');
    const progressChart = new Chart(progressCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($charts['progress']['labels']) !!},
            datasets: [
                {
                    label: 'حفظ',
                    data: {!! json_encode($charts['progress']['memorization']) !!},
                    borderColor: 'rgba(0, 123, 255, 1)',
                    backgroundColor: 'transparent',
                    tension: 0.4
                },
                {
                    label: 'تجويد',
                    data: {!! json_encode($charts['progress']['tajweed']) !!},
                    borderColor: 'rgba(23, 162, 184, 1)',
                    backgroundColor: 'transparent',
                    tension: 0.4
                },
                {
                    label: 'تلاوة',
                    data: {!! json_encode($charts['progress']['recitation']) !!},
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'transparent',
                    tension: 0.4
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
                    max: 100
                }
            }
        }
    });
});
</script>
@endsection
