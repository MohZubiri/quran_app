@extends('layouts.admin')

@section('title', 'تقرير تقدم الطالب')

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
        <form action="{{ url('/admin/reports/student-progress') }}" method="GET" id="filter-form">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="student_id" class="form-label">الطالب</label>
                    <select class="form-select" id="student_id" name="student_id" required>
                        <option value="">اختر الطالب...</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                        @endforeach
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
                
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                    <a href="{{ url('/admin/reports/student-progress') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@if(isset($student) && $student)
<!-- معلومات الطالب -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">معلومات الطالب</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th class="bg-light" width="30%">اسم الطالب</th>
                        <td>{{ $student->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">الحلقة</th>
                        <td>{{ $student->group->name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">الفرع</th>
                        <td>{{ $student->group->branch->name }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th class="bg-light" width="30%">رقم الهاتف</th>
                        <td>{{ $student->phone }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">تاريخ الالتحاق</th>
                        <td>{{ $student->enrollment_date }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">الحالة</th>
                        <td>
                            <span class="badge {{ $student->status == 'active' ? 'bg-success' : 'bg-warning' }}">
                                {{ $student->status == 'active' ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ملخص التقدم -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">ملخص التقدم</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">نسبة الحضور</h6>
                                <p class="card-text display-6">{{ $summary['attendance_rate'] ?? 0 }}%</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">متوسط الحفظ</h6>
                                <p class="card-text display-6">{{ $summary['memorization_avg'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">متوسط التجويد</h6>
                                <p class="card-text display-6">{{ $summary['tajweed_avg'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">المتوسط العام</h6>
                                <p class="card-text display-6">{{ $summary['overall_avg'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- مخطط تقدم الطالب -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">تطور أداء الطالب</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="studentProgressChart" width="100%" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- تفاصيل التقييمات -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">تفاصيل التقييمات</h5>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs" id="gradesTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="memorization-tab" data-bs-toggle="tab" data-bs-target="#memorization" type="button" role="tab" aria-controls="memorization" aria-selected="true">الحفظ</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tajweed-tab" data-bs-toggle="tab" data-bs-target="#tajweed" type="button" role="tab" aria-controls="tajweed" aria-selected="false">التجويد</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="recitation-tab" data-bs-toggle="tab" data-bs-target="#recitation" type="button" role="tab" aria-controls="recitation" aria-selected="false">التلاوة</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="behavior-tab" data-bs-toggle="tab" data-bs-target="#behavior" type="button" role="tab" aria-controls="behavior" aria-selected="false">السلوك</button>
            </li>
        </ul>
        <div class="tab-content pt-3" id="gradesTabContent">
            <div class="tab-pane fade show active" id="memorization" role="tabpanel" aria-labelledby="memorization-tab">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>السورة</th>
                                <th>الآيات</th>
                                <th>الدرجة</th>
                                <th>الملاحظات</th>
                                <th>المعلم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grades['memorization'] ?? [] as $index => $grade)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $grade->date }}</td>
                                <td>{{ $grade->surah }}</td>
                                <td>{{ $grade->verses }}</td>
                                <td>
                                    <span class="badge {{ $grade->grade >= 90 ? 'bg-success' : ($grade->grade >= 70 ? 'bg-info' : ($grade->grade >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                        {{ $grade->grade }}
                                    </span>
                                </td>
                                <td>{{ $grade->notes }}</td>
                                <td>{{ $grade->teacher->name }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">لا توجد تقييمات للحفظ</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="tajweed" role="tabpanel" aria-labelledby="tajweed-tab">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>الموضوع</th>
                                <th>الدرجة</th>
                                <th>الملاحظات</th>
                                <th>المعلم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grades['tajweed'] ?? [] as $index => $grade)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $grade->date }}</td>
                                <td>{{ $grade->topic }}</td>
                                <td>
                                    <span class="badge {{ $grade->grade >= 90 ? 'bg-success' : ($grade->grade >= 70 ? 'bg-info' : ($grade->grade >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                        {{ $grade->grade }}
                                    </span>
                                </td>
                                <td>{{ $grade->notes }}</td>
                                <td>{{ $grade->teacher->name }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">لا توجد تقييمات للتجويد</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="recitation" role="tabpanel" aria-labelledby="recitation-tab">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>السورة</th>
                                <th>الدرجة</th>
                                <th>الملاحظات</th>
                                <th>المعلم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grades['recitation'] ?? [] as $index => $grade)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $grade->date }}</td>
                                <td>{{ $grade->surah }}</td>
                                <td>
                                    <span class="badge {{ $grade->grade >= 90 ? 'bg-success' : ($grade->grade >= 70 ? 'bg-info' : ($grade->grade >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                        {{ $grade->grade }}
                                    </span>
                                </td>
                                <td>{{ $grade->notes }}</td>
                                <td>{{ $grade->teacher->name }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">لا توجد تقييمات للتلاوة</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="behavior" role="tabpanel" aria-labelledby="behavior-tab">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>الدرجة</th>
                                <th>الملاحظات</th>
                                <th>المعلم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grades['behavior'] ?? [] as $index => $grade)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $grade->date }}</td>
                                <td>
                                    <span class="badge {{ $grade->grade >= 90 ? 'bg-success' : ($grade->grade >= 70 ? 'bg-info' : ($grade->grade >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                        {{ $grade->grade }}
                                    </span>
                                </td>
                                <td>{{ $grade->notes }}</td>
                                <td>{{ $grade->teacher->name }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">لا توجد تقييمات للسلوك</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- سجل الحضور -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">سجل الحضور</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">إحصائيات الحضور</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="attendanceChart" width="100%" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendance ?? [] as $index => $record)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $record->date }}</td>
                                <td>
                                    @if($record->status == 'present')
                                        <span class="badge bg-success">حاضر</span>
                                    @elseif($record->status == 'absent')
                                        <span class="badge bg-danger">غائب</span>
                                    @elseif($record->status == 'late')
                                        <span class="badge bg-warning">متأخر</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $record->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $record->notes }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">لا توجد سجلات حضور</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- توصيات وملاحظات -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">التوصيات والملاحظات</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h6 class="fw-bold">ملاحظات وتوصيات:</h6>
            <ul>
                @forelse($recommendations ?? [] as $recommendation)
                <li>{{ $recommendation }}</li>
                @empty
                <li>لا توجد توصيات متاحة حاليًا.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@else
<div class="alert alert-info">
    <p>يرجى اختيار طالب لعرض تقرير التقدم الخاص به.</p>
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($student) && $student)
    // رسم بياني لتطور أداء الطالب
    const progressCtx = document.getElementById('studentProgressChart').getContext('2d');
    const progressChart = new Chart(progressCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($charts['progress']['labels'] ?? []) !!},
            datasets: [
                {
                    label: 'الحفظ',
                    data: {!! json_encode($charts['progress']['memorization'] ?? []) !!},
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'التجويد',
                    data: {!! json_encode($charts['progress']['tajweed'] ?? []) !!},
                    borderColor: 'rgba(23, 162, 184, 1)',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'التلاوة',
                    data: {!! json_encode($charts['progress']['recitation'] ?? []) !!},
                    borderColor: 'rgba(0, 123, 255, 1)',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'السلوك',
                    data: {!! json_encode($charts['progress']['behavior'] ?? []) !!},
                    borderColor: 'rgba(255, 193, 7, 1)',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    rtl: true
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
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

    // رسم بياني للحضور
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(attendanceCtx, {
        type: 'pie',
        data: {
            labels: ['حاضر', 'غائب', 'متأخر'],
            datasets: [{
                data: [
                    {{ $attendance_stats['present'] ?? 0 }}, 
                    {{ $attendance_stats['absent'] ?? 0 }}, 
                    {{ $attendance_stats['late'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)'
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
    @endif

    // تصدير البيانات
    document.getElementById('exportExcelBtn')?.addEventListener('click', function() {
        window.location.href = "{{ url('/admin/exports/student-progress/excel') }}" + window.location.search;
    });

    document.getElementById('exportPdfBtn')?.addEventListener('click', function() {
        window.location.href = "{{ url('/admin/exports/student-progress/pdf') }}" + window.location.search;
    });
});
</script>
@endsection
