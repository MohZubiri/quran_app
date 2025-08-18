@extends('layouts.admin')

@section('title', 'تقرير مقارنة الطلاب')

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
        <h5 class="card-title">فلاتر المقارنة</h5>
    </div>
    <div class="card-body">
        <form action="{{ url('/admin/reports/student-comparison') }}" method="GET" id="filter-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="branch_id" class="form-label">الفرع</label>
                    <select class="form-select" id="branch_id" name="branch_id">
                        <option value="">جميع الفروع</option>
                        @foreach($branches ?? [] as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="group_id" class="form-label">الحلقة</label>
                    <select class="form-select" id="group_id" name="group_id">
                        <option value="">جميع الحلقات</option>
                        @foreach($groups ?? [] as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="comparison_type" class="form-label">نوع المقارنة</label>
                    <select class="form-select" id="comparison_type" name="comparison_type">
                        <option value="all" {{ request('comparison_type') == 'all' ? 'selected' : '' }}>جميع المعايير</option>
                        <option value="memorization" {{ request('comparison_type') == 'memorization' ? 'selected' : '' }}>الحفظ</option>
                        <option value="tajweed" {{ request('comparison_type') == 'tajweed' ? 'selected' : '' }}>التجويد</option>
                        <option value="recitation" {{ request('comparison_type') == 'recitation' ? 'selected' : '' }}>التلاوة</option>
                        <option value="behavior" {{ request('comparison_type') == 'behavior' ? 'selected' : '' }}>السلوك</option>
                        <option value="attendance" {{ request('comparison_type') == 'attendance' ? 'selected' : '' }}>الحضور</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="date_from" class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-4">
                    <label for="date_to" class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-4">
                    <label for="limit" class="form-label">عدد الطلاب</label>
                    <select class="form-select" id="limit" name="limit">
                        <option value="5" {{ request('limit') == 5 ? 'selected' : '' }}>أفضل 5 طلاب</option>
                        <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>أفضل 10 طلاب</option>
                        <option value="20" {{ request('limit') == 20 ? 'selected' : '' }}>أفضل 20 طلاب</option>
                        <option value="all" {{ request('limit') == 'all' ? 'selected' : '' }}>جميع الطلاب</option>
                    </select>
                </div>
                
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                    <a href="{{ url('/admin/reports/student-comparison') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@if(isset($students) && count($students) > 0)
<!-- مخططات المقارنة -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">مقارنة أداء الطلاب</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">متوسط الدرجات</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="gradesComparisonChart" width="100%" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">نسبة الحضور</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="attendanceComparisonChart" width="100%" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">مقارنة شاملة</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="overallComparisonChart" width="100%" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- جدول مقارنة الطلاب -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">جدول مقارنة الطلاب</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="comparison-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الطالب</th>
                        <th>الحلقة</th>
                        <th>نسبة الحضور</th>
                        <th>متوسط الحفظ</th>
                        <th>متوسط التجويد</th>
                        <th>متوسط التلاوة</th>
                        <th>متوسط السلوك</th>
                        <th>المتوسط العام</th>
                        <th>المستوى</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $student)
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
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ $student['attendance_rate'] >= 90 ? 'bg-success' : ($student['attendance_rate'] >= 70 ? 'bg-info' : ($student['attendance_rate'] >= 50 ? 'bg-warning' : 'bg-danger')) }}" 
                                     role="progressbar" style="width: {{ $student['attendance_rate'] }}%;" 
                                     aria-valuenow="{{ $student['attendance_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $student['attendance_rate'] }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $student['memorization_avg'] >= 90 ? 'bg-success' : ($student['memorization_avg'] >= 70 ? 'bg-info' : ($student['memorization_avg'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $student['memorization_avg'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $student['tajweed_avg'] >= 90 ? 'bg-success' : ($student['tajweed_avg'] >= 70 ? 'bg-info' : ($student['tajweed_avg'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $student['tajweed_avg'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $student['recitation_avg'] >= 90 ? 'bg-success' : ($student['recitation_avg'] >= 70 ? 'bg-info' : ($student['recitation_avg'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $student['recitation_avg'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $student['behavior_avg'] >= 90 ? 'bg-success' : ($student['behavior_avg'] >= 70 ? 'bg-info' : ($student['behavior_avg'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $student['behavior_avg'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $student['average'] >= 90 ? 'bg-success' : ($student['average'] >= 80 ? 'bg-info' : ($student['average'] >= 70 ? 'bg-primary' : ($student['average'] >= 60 ? 'bg-warning' : 'bg-danger'))) }} fs-6">
                                {{ $student['average'] }}
                            </span>
                        </td>
                        <td>
                            @if($student['average'] >= 90)
                                <span class="badge bg-success">ممتاز</span>
                            @elseif($student['average'] >= 80)
                                <span class="badge bg-info">جيد جدًا</span>
                            @elseif($student['average'] >= 70)
                                <span class="badge bg-primary">جيد</span>
                            @elseif($student['average'] >= 60)
                                <span class="badge bg-warning">مقبول</span>
                            @else
                                <span class="badge bg-danger">ضعيف</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ url('/admin/students/' . $student['id']) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('/admin/reports/student-progress?student_id=' . $student['id']) }}" class="btn btn-sm btn-primary">
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

<!-- تحليل المقارنة -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">تحليل المقارنة</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="card-title">نقاط القوة والضعف</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>المعيار</th>
                                    <th>أعلى متوسط</th>
                                    <th>أدنى متوسط</th>
                                    <th>المتوسط العام</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>الحفظ</td>
                                    <td>{{ $analysis['memorization']['max'] ?? 0 }}</td>
                                    <td>{{ $analysis['memorization']['min'] ?? 0 }}</td>
                                    <td>{{ $analysis['memorization']['avg'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>التجويد</td>
                                    <td>{{ $analysis['tajweed']['max'] ?? 0 }}</td>
                                    <td>{{ $analysis['tajweed']['min'] ?? 0 }}</td>
                                    <td>{{ $analysis['tajweed']['avg'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>التلاوة</td>
                                    <td>{{ $analysis['recitation']['max'] ?? 0 }}</td>
                                    <td>{{ $analysis['recitation']['min'] ?? 0 }}</td>
                                    <td>{{ $analysis['recitation']['avg'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>السلوك</td>
                                    <td>{{ $analysis['behavior']['max'] ?? 0 }}</td>
                                    <td>{{ $analysis['behavior']['min'] ?? 0 }}</td>
                                    <td>{{ $analysis['behavior']['avg'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>الحضور</td>
                                    <td>{{ $analysis['attendance']['max'] ?? 0 }}%</td>
                                    <td>{{ $analysis['attendance']['min'] ?? 0 }}%</td>
                                    <td>{{ $analysis['attendance']['avg'] ?? 0 }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="card-title">التوصيات</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6 class="fw-bold">توصيات عامة:</h6>
                            <ul>
                                @forelse($recommendations ?? [] as $recommendation)
                                <li>{{ $recommendation }}</li>
                                @empty
                                <li>لا توجد توصيات متاحة حاليًا.</li>
                                @endforelse
                            </ul>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6 class="fw-bold">مجالات تحتاج إلى تحسين:</h6>
                            <ul>
                                @forelse($improvement_areas ?? [] as $area)
                                <li>{{ $area }}</li>
                                @empty
                                <li>لا توجد مجالات محددة للتحسين حاليًا.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-info">
    <p>يرجى تحديد معايير المقارنة لعرض البيانات.</p>
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($students) && count($students) > 0)
    // تحديث قوائم الحلقات عند تغيير الفرع
    document.getElementById('branch_id').addEventListener('change', function() {
        const branchId = this.value;
        
        // يمكن إضافة كود Ajax هنا لتحديث قوائم الحلقات بناءً على الفرع المختار
    });
    
    // مخطط مقارنة الدرجات
    const gradesCtx = document.getElementById('gradesComparisonChart').getContext('2d');
    const gradesChart = new Chart(gradesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($students, 'name')) !!},
            datasets: [
                {
                    label: 'الحفظ',
                    data: {!! json_encode(array_column($students, 'memorization_avg')) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderWidth: 1
                },
                {
                    label: 'التجويد',
                    data: {!! json_encode(array_column($students, 'tajweed_avg')) !!},
                    backgroundColor: 'rgba(23, 162, 184, 0.7)',
                    borderWidth: 1
                },
                {
                    label: 'التلاوة',
                    data: {!! json_encode(array_column($students, 'recitation_avg')) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                    borderWidth: 1
                },
                {
                    label: 'السلوك',
                    data: {!! json_encode(array_column($students, 'behavior_avg')) !!},
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderWidth: 1
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
    
    // مخطط مقارنة الحضور
    const attendanceCtx = document.getElementById('attendanceComparisonChart').getContext('2d');
    const attendanceChart = new Chart(attendanceCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($students, 'name')) !!},
            datasets: [
                {
                    label: 'نسبة الحضور',
                    data: {!! json_encode(array_column($students, 'attendance_rate')) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                    borderWidth: 1
                }
            ]
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
    
    // مخطط المقارنة الشاملة
    const overallCtx = document.getElementById('overallComparisonChart').getContext('2d');
    const overallChart = new Chart(overallCtx, {
        type: 'radar',
        data: {
            labels: ['الحفظ', 'التجويد', 'التلاوة', 'السلوك', 'الحضور'],
            datasets: [
                @foreach($students as $index => $student)
                {
                    label: '{{ $student['name'] }}',
                    data: [
                        {{ $student['memorization_avg'] }},
                        {{ $student['tajweed_avg'] }},
                        {{ $student['recitation_avg'] }},
                        {{ $student['behavior_avg'] }},
                        {{ $student['attendance_rate'] }}
                    ],
                    fill: true,
                    backgroundColor: 'rgba({{ 50 + ($index * 40) }}, {{ 100 + ($index * 30) }}, {{ 150 + ($index * 20) }}, 0.2)',
                    borderColor: 'rgba({{ 50 + ($index * 40) }}, {{ 100 + ($index * 30) }}, {{ 150 + ($index * 20) }}, 1)',
                    pointBackgroundColor: 'rgba({{ 50 + ($index * 40) }}, {{ 100 + ($index * 30) }}, {{ 150 + ($index * 20) }}, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba({{ 50 + ($index * 40) }}, {{ 100 + ($index * 30) }}, {{ 150 + ($index * 20) }}, 1)'
                }{{ $index < count($students) - 1 ? ',' : '' }}
                @endforeach
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
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
    @endif

    // تصدير البيانات
    document.getElementById('exportExcelBtn')?.addEventListener('click', function() {
        window.location.href = "{{ url('/admin/exports/student-comparison/excel') }}" + window.location.search;
    });

    document.getElementById('exportPdfBtn')?.addEventListener('click', function() {
        window.location.href = "{{ url('/admin/exports/student-comparison/pdf') }}" + window.location.search;
    });
});
</script>
@endsection
