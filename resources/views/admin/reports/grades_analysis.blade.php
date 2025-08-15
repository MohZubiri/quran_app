@extends('layouts.admin')

@section('title', 'تحليل التقييمات')

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
        <form action="{{ route('admin.reports.grades_analysis') }}" method="GET" id="filter-form">
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
                    <label for="teacher_id" class="form-label">المعلم</label>
                    <select class="form-select" id="teacher_id" name="teacher_id">
                        <option value="">جميع المعلمين</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="grade_type" class="form-label">نوع التقييم</label>
                    <select class="form-select" id="grade_type" name="grade_type">
                        <option value="">جميع أنواع التقييم</option>
                        <option value="memorization" {{ request('grade_type') == 'memorization' ? 'selected' : '' }}>الحفظ</option>
                        <option value="tajweed" {{ request('grade_type') == 'tajweed' ? 'selected' : '' }}>التجويد</option>
                        <option value="recitation" {{ request('grade_type') == 'recitation' ? 'selected' : '' }}>التلاوة</option>
                        <option value="behavior" {{ request('grade_type') == 'behavior' ? 'selected' : '' }}>السلوك</option>
                        <option value="achievement" {{ request('grade_type') == 'achievement' ? 'selected' : '' }}>الإنجاز</option>
                        <option value="attendance" {{ request('grade_type') == 'attendance' ? 'selected' : '' }}>الحضور</option>
                        <option value="appearance" {{ request('grade_type') == 'appearance' ? 'selected' : '' }}>المظهر</option>
                        <option value="plan_score" {{ request('grade_type') == 'plan_score' ? 'selected' : '' }}>الإنجاز اليومي من الخطة</option>
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
                    <a href="{{ route('admin.reports.grades_analysis') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- نظرة عامة على التقييمات -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">نظرة عامة على التقييمات</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">إجمالي التقييمات</h6>
                                <p class="card-text display-6">{{ $overview['total_grades'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">متوسط التقييمات</h6>
                                <p class="card-text display-6">{{ $overview['average_score'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">أعلى درجة</h6>
                                <p class="card-text display-6">{{ $overview['highest_score'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">أقل درجة</h6>
                                <p class="card-text display-6">{{ $overview['lowest_score'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">توزيع الدرجات</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="gradesDistributionChart" width="400" height="300"></canvas>
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

<!-- تحليل التقييمات حسب الحلقة -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">تحليل التقييمات حسب الحلقة</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الحلقة</th>
                        <th>الفرع</th>
                        <th>المعلم</th>
                        <th>عدد التقييمات</th>
                        <th>متوسط الحفظ</th>
                        <th>متوسط التجويد</th>
                        <th>متوسط التلاوة</th>
                        <th>متوسط السلوك</th>
                        <th>متوسط الإنجاز</th>
                        <th>متوسط الحضور</th>
                        <th>متوسط المظهر</th>
                        <th>متوسط الإنجاز اليومي</th>
                        <th>المتوسط العام</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupsAnalysis as $index => $group)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('admin.groups.show', $group['id']) }}">
                                {{ $group['name'] }}
                            </a>
                        </td>
                        <td>{{ $group['branch_name'] }}</td>
                        <td>
                            <a href="{{ route('admin.teachers.show', $group['teacher_id']) }}">
                                {{ $group['teacher_name'] }}
                            </a>
                        </td>
                        <td>{{ $group['grades_count'] }}</td>
                        <td>
                            <span class="badge {{ $group['memorization_avg'] >= 90 ? 'bg-success' : ($group['memorization_avg'] >= 70 ? 'bg-info' : ($group['memorization_avg'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $group['memorization_avg'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $group['tajweed_avg'] >= 90 ? 'bg-success' : ($group['tajweed_avg'] >= 70 ? 'bg-info' : ($group['tajweed_avg'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $group['tajweed_avg'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $group['recitation_avg'] >= 90 ? 'bg-success' : ($group['recitation_avg'] >= 70 ? 'bg-info' : ($group['recitation_avg'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $group['recitation_avg'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $group['behavior_avg'] >= 90 ? 'bg-success' : ($group['behavior_avg'] >= 70 ? 'bg-info' : ($group['behavior_avg'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $group['behavior_avg'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $group['achievement_avg'] >= 90 ? 'bg-success' : ($group['achievement_avg'] >= 70 ? 'bg-info' : ($group['achievement_avg'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $group['achievement_avg'] ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $group['attendance_avg'] >= 90 ? 'bg-success' : ($group['attendance_avg'] >= 70 ? 'bg-info' : ($group['attendance_avg'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $group['attendance_avg'] ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $group['appearance_avg'] >= 90 ? 'bg-success' : ($group['appearance_avg'] >= 70 ? 'bg-info' : ($group['appearance_avg'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $group['appearance_avg'] ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background-color: {{ $group['plan_score_avg'] >= 90 ? '#198754' : ($group['plan_score_avg'] >= 70 ? '#0dcaf0' : ($group['plan_score_avg'] >= 50 ? '#ffc107' : '#dc3545')) }}">
                                {{ $group['plan_score_avg'] ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $group['overall_avg'] >= 90 ? 'bg-success' : ($group['overall_avg'] >= 80 ? 'bg-info' : ($group['overall_avg'] >= 70 ? 'bg-primary' : ($group['overall_avg'] >= 60 ? 'bg-warning' : 'bg-danger'))) }} fs-6">
                                {{ $group['overall_avg'] }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.groups.show', $group['id']) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.reports.group_grades', ['group_id' => $group['id']]) }}" class="btn btn-sm btn-primary">
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

<!-- تحليل التقييمات حسب المعلم -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title">تحليل التقييمات حسب المعلم</h5>
    </div>
    <div class="card-body">
        <canvas id="teacherGradesChart" width="800" height="400"></canvas>
    </div>
</div>

<!-- التقييمات الأحدث -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">التقييمات الأحدث</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>الطالب</th>
                        <th>الحلقة</th>
                        <th>المعلم</th>
                        <th>نوع التقييم</th>
                        <th>الدرجة</th>
                        <th>الآيات المغطاة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestGrades as $index => $grade)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $grade['date'] }}</td>
                        <td>
                            <a href="{{ route('admin.students.show', $grade['student_id']) }}">
                                {{ $grade['student_name'] }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.groups.show', $grade['group_id']) }}">
                                {{ $grade['group_name'] }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.teachers.show', $grade['teacher_id']) }}">
                                {{ $grade['teacher_name'] }}
                            </a>
                        </td>
                        <td>
                            @if($grade['grade_type'] == 'memorization')
                                <span class="badge bg-primary">الحفظ</span>
                            @elseif($grade['grade_type'] == 'tajweed')
                                <span class="badge bg-info">التجويد</span>
                            @elseif($grade['grade_type'] == 'recitation')
                                <span class="badge bg-success">التلاوة</span>
                            @elseif($grade['grade_type'] == 'behavior')
                                <span class="badge bg-warning">السلوك</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $grade['score'] >= 90 ? 'bg-success' : ($grade['score'] >= 70 ? 'bg-info' : ($grade['score'] >= 50 ? 'bg-warning' : 'bg-danger')) }} fs-6">
                                {{ $grade['score'] }}
                            </span>
                        </td>
                        <td>{{ $grade['verses_covered'] ?: 'غير محدد' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.grades.show', $grade['id']) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.grades.edit', $grade['id']) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
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
    // تحديث قوائم عند تغيير الفرع
    document.getElementById('branch_id').addEventListener('change', function() {
        const branchId = this.value;
        
        // يمكن إضافة كود Ajax هنا لتحديث القوائم
    });
    
    // رسم بياني لتوزيع الدرجات
    const distributionCtx = document.getElementById('gradesDistributionChart').getContext('2d');
    const distributionChart = new Chart(distributionCtx, {
        type: 'bar',
        data: {
            labels: ['90-100', '80-89', '70-79', '60-69', '50-59', '< 50'],
            datasets: [{
                label: 'عدد التقييمات',
                data: {!! json_encode($charts['distribution']) !!},
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

    // رسم بياني لمتوسط التقييمات حسب النوع
    const typeCtx = document.getElementById('gradesByTypeChart').getContext('2d');
    const typeChart = new Chart(typeCtx, {
        type: 'radar',
        data: {
            labels: ['الحفظ', 'التجويد', 'التلاوة', 'السلوك'],
            datasets: [{
                label: 'متوسط الدرجات',
                data: [
                    {{ $overview['memorization_avg'] }},
                    {{ $overview['tajweed_avg'] }},
                    {{ $overview['recitation_avg'] }},
                    {{ $overview['behavior_avg'] }}
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

    // رسم بياني للتقييمات حسب المعلم
    const teacherCtx = document.getElementById('teacherGradesChart').getContext('2d');
    const teacherChart = new Chart(teacherCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($teachersAnalysis, 'name')) !!},
            datasets: [
                {
                    label: 'الحفظ',
                    data: {!! json_encode(array_column($teachersAnalysis, 'memorization_avg')) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                },
                {
                    label: 'التجويد',
                    data: {!! json_encode(array_column($teachersAnalysis, 'tajweed_avg')) !!},
                    backgroundColor: 'rgba(23, 162, 184, 0.7)',
                },
                {
                    label: 'التلاوة',
                    data: {!! json_encode(array_column($teachersAnalysis, 'recitation_avg')) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                },
                {
                    label: 'السلوك',
                    data: {!! json_encode(array_column($teachersAnalysis, 'behavior_avg')) !!},
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
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

    // تصدير البيانات
    document.getElementById('exportExcelBtn').addEventListener('click', function() {
        window.location.href = "{{ route('admin.exports.grades', ['format' => 'excel']) }}" + window.location.search;
    });

    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        window.location.href = "{{ route('admin.exports.grades', ['format' => 'pdf']) }}" + window.location.search;
    });
});
</script>
@endsection
