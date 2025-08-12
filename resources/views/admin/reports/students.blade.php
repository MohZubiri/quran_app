@extends('layouts.admin')

@section('title', 'تقرير أداء الطلاب')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
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
        <form action="{{ route('admin.reports.students') }}" method="GET" id="filter-form">
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
                    <a href="{{ route('admin.reports.students') }}" class="btn btn-secondary">
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
                <h5 class="card-title">نظرة عامة على أداء الطلاب</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">عدد الطلاب</h6>
                                <p class="card-text display-6">{{ $overview['total_students'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">متوسط الحضور</h6>
                                <p class="card-text display-6">{{ $overview['avg_attendance'] }}%</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">متوسط التقييمات</h6>
                                <p class="card-text display-6">{{ $overview['avg_grades'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">إجمالي التقييمات</h6>
                                <p class="card-text display-6">{{ $overview['total_grades'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">توزيع مستويات الطلاب</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="studentsLevelChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">متوسط التقييمات حسب النوع</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="gradesByTypeChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- جدول أداء الطلاب -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title">تقرير أداء الطلاب</h5>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-primary" id="showAllBtn">عرض جميع البيانات</button>
            <button type="button" class="btn btn-sm btn-outline-success" id="showTopBtn">أفضل الطلاب</button>
            <button type="button" class="btn btn-sm btn-outline-warning" id="showNeedsImprovementBtn">بحاجة لتحسين</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="student-performance-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الطالب</th>
                        <th>الحلقة</th>
                        <th>نسبة الحضور</th>
                        <th>متوسط تقييم الحفظ</th>
                        <th>متوسط تقييم التجويد</th>
                        <th>متوسط تقييم التلاوة</th>
                        <th>متوسط تقييم السلوك</th>
                        <th>المتوسط العام</th>
                        <th>المستوى</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $student)
                    <tr data-average="{{ $student['average'] }}" class="{{ $student['average'] >= 90 ? 'table-success' : ($student['average'] < 60 ? 'table-danger' : '') }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('admin.students.show', $student['id']) }}">
                                {{ $student['name'] }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.groups.show', $student['group_id']) }}">
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
                                <a href="{{ route('admin.students.show', $student['id']) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.reports.student_progress', ['student_id' => $student['id']]) }}" class="btn btn-sm btn-primary">
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

<!-- توصيات وملاحظات -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">التوصيات والملاحظات</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold">الطلاب المتميزون:</h6>
                <ul class="list-group">
                    @foreach($recommendations['top_students'] as $student)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.students.show', $student['id']) }}">{{ $student['name'] }}</a>
                        <span class="badge bg-success rounded-pill">{{ $student['average'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">الطلاب بحاجة إلى متابعة:</h6>
                <ul class="list-group">
                    @foreach($recommendations['needs_improvement'] as $student)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.students.show', $student['id']) }}">{{ $student['name'] }}</a>
                        <span class="badge bg-danger rounded-pill">{{ $student['average'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        
        <div class="alert alert-info mt-4">
            <h6 class="fw-bold">ملاحظات وتوصيات عامة:</h6>
            <ul>
                @foreach($recommendations['general'] as $note)
                <li>{{ $note }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث قوائم الحلقات والطلاب عند تغيير الفرع
    document.getElementById('branch_id').addEventListener('change', function() {
        const branchId = this.value;
        
        // يمكن إضافة كود Ajax هنا لتحديث قوائم الحلقات والطلاب بناءً على الفرع المختار
    });
    
    // رسم بياني لتوزيع مستويات الطلاب
    const levelCtx = document.getElementById('studentsLevelChart').getContext('2d');
    const levelChart = new Chart(levelCtx, {
        type: 'pie',
        data: {
            labels: ['ممتاز', 'جيد جدًا', 'جيد', 'مقبول', 'ضعيف'],
            datasets: [{
                data: {!! json_encode($charts['levels']) !!},
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
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: true
                }
            }
        }
    });

    // رسم بياني لمتوسط التقييمات حسب النوع
    const typeCtx = document.getElementById('gradesByTypeChart').getContext('2d');
    const typeChart = new Chart(typeCtx, {
        type: 'bar',
        data: {
            labels: ['حفظ', 'تجويد', 'تلاوة', 'سلوك'],
            datasets: [{
                label: 'متوسط الدرجات',
                data: {!! json_encode($charts['grade_types']) !!},
                backgroundColor: [
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)'
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
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // التبديل بين عرض جميع الطلاب، أفضل الطلاب، والطلاب بحاجة لتحسين
    document.getElementById('showAllBtn').addEventListener('click', function() {
        const rows = document.querySelectorAll('#student-performance-table tbody tr');
        rows.forEach(row => {
            row.style.display = '';
        });
    });

    document.getElementById('showTopBtn').addEventListener('click', function() {
        const rows = document.querySelectorAll('#student-performance-table tbody tr');
        rows.forEach(row => {
            const average = parseFloat(row.getAttribute('data-average'));
            row.style.display = average >= 90 ? '' : 'none';
        });
    });

    document.getElementById('showNeedsImprovementBtn').addEventListener('click', function() {
        const rows = document.querySelectorAll('#student-performance-table tbody tr');
        rows.forEach(row => {
            const average = parseFloat(row.getAttribute('data-average'));
            row.style.display = average < 60 ? '' : 'none';
        });
    });

    // تصدير البيانات
    document.getElementById('exportExcelBtn').addEventListener('click', function() {
        window.location.href = "{{ route('admin.exports.students', ['format' => 'excel']) }}" + window.location.search;
    });

    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        window.location.href = "{{ route('admin.exports.students', ['format' => 'pdf']) }}" + window.location.search;
    });
});
</script>
@endsection
