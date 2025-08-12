@extends('layouts.student')

@section('title', 'سجل الحضور')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">سجل الحضور</h1>
    </div>
</div>

<div class="row mb-4">
    <!-- Attendance Summary Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-gradient-info">
                <h5 class="mb-0">ملخص الحضور</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <canvas id="attendanceChart" width="180" height="180"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <h2 class="mb-0">{{ $attendanceRate }}%</h2>
                                <small class="text-muted">نسبة الحضور</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-9">
                        <div class="row text-center">
                            <div class="col-md-3 col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h3 class="text-success mb-0">{{ $presentCount }}</h3>
                                    <p class="text-muted mb-0">حاضر</p>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h3 class="text-danger mb-0">{{ $absentCount }}</h3>
                                    <p class="text-muted mb-0">غائب</p>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h3 class="text-warning mb-0">{{ $lateCount }}</h3>
                                    <p class="text-muted mb-0">متأخر</p>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h3 class="text-info mb-0">{{ $excusedCount }}</h3>
                                    <p class="text-muted mb-0">معذور</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6>معدل الحضور الشهري</h6>
                            <canvas id="monthlyAttendanceChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">سجل الحضور الكامل</h5>
                <div>
                    <select id="attendanceStatusFilter" class="form-select form-select-sm">
                        <option value="all">كل الحالات</option>
                        <option value="present">حاضر</option>
                        <option value="absent">غائب</option>
                        <option value="late">متأخر</option>
                        <option value="excused">معذور</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="attendanceTable">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>اليوم</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendance as $record)
                                <tr data-status="{{ $record->status }}">
                                    <td>{{ $record->date->format('Y-m-d') }}</td>
                                    <td>{{ $record->date->translatedFormat('l') }}</td>
                                    <td>
                                        @if($record->status == 'present')
                                            <span class="badge bg-success">حاضر</span>
                                        @elseif($record->status == 'absent')
                                            <span class="badge bg-danger">غائب</span>
                                        @elseif($record->status == 'late')
                                            <span class="badge bg-warning">متأخر</span>
                                        @elseif($record->status == 'excused')
                                            <span class="badge bg-info">معذور</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->notes)
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $record->notes }}">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد سجلات حضور</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $attendance->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Attendance Status Filter
    document.getElementById('attendanceStatusFilter').addEventListener('change', function() {
        const selectedStatus = this.value;
        const rows = document.querySelectorAll('#attendanceTable tbody tr');
        
        rows.forEach(row => {
            if (selectedStatus === 'all' || row.dataset.status === selectedStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Attendance Doughnut Chart
    const attendanceRate = {{ $attendanceRate }};
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['حضور', 'غياب'],
            datasets: [{
                data: [attendanceRate, 100 - attendanceRate],
                backgroundColor: ['#36b9cc', '#e0e0e0'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '75%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    });
    
    // Monthly Attendance Chart
    const monthlyCtx = document.getElementById('monthlyAttendanceChart').getContext('2d');
    const monthlyData = @json($monthlyData);
    
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'نسبة الحضور',
                data: monthlyData.map(item => item.rate),
                backgroundColor: 'rgba(54, 185, 204, 0.2)',
                borderColor: '#36b9cc',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#36b9cc',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
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
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '%';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
