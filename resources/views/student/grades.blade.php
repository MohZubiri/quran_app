@extends('layouts.student')

@section('title', 'الدرجات')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">سجل الدرجات</h1>
    </div>
</div>

<div class="row mb-4">
    <!-- Grade Summary Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-gradient-success">
                <h5 class="mb-0">ملخص الدرجات</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <div class="border rounded p-3">
                            <h2>{{ $overallAverage }}/10</h2>
                            <p class="text-muted mb-0">المتوسط العام</p>
                        </div>
                    </div>
                    
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6>الحفظ</h6>
                                <div class="d-flex justify-content-between">
                                    <span>{{ $gradeAverages['memorization'] }}/10</span>
                                    <span>{{ $gradeAverages['memorization'] * 10 }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-memorization" role="progressbar" style="width: {{ $gradeAverages['memorization'] * 10 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <h6>التجويد</h6>
                                <div class="d-flex justify-content-between">
                                    <span>{{ $gradeAverages['tajweed'] }}/10</span>
                                    <span>{{ $gradeAverages['tajweed'] * 10 }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-tajweed" role="progressbar" style="width: {{ $gradeAverages['tajweed'] * 10 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <h6>التلاوة</h6>
                                <div class="d-flex justify-content-between">
                                    <span>{{ $gradeAverages['recitation'] }}/10</span>
                                    <span>{{ $gradeAverages['recitation'] * 10 }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-recitation" role="progressbar" style="width: {{ $gradeAverages['recitation'] * 10 }}%"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <h6>السلوك</h6>
                                <div class="d-flex justify-content-between">
                                    <span>{{ $gradeAverages['behavior'] }}/10</span>
                                    <span>{{ $gradeAverages['behavior'] * 10 }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-behavior" role="progressbar" style="width: {{ $gradeAverages['behavior'] * 10 }}%"></div>
                                </div>
                            </div>
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
                <h5 class="mb-0">سجل الدرجات الكامل</h5>
                <div>
                    <select id="gradeTypeFilter" class="form-select form-select-sm">
                        <option value="all">كل أنواع التقييم</option>
                        <option value="memorization">الحفظ</option>
                        <option value="tajweed">التجويد</option>
                        <option value="recitation">التلاوة</option>
                        <option value="behavior">السلوك</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="gradesTable">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>المادة</th>
                                <th>المعلم</th>
                                <th>نوع التقييم</th>
                                <th>الدرجة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grades as $grade)
                                <tr data-grade-type="{{ $grade->grade_type }}">
                                    <td>{{ $grade->date->format('Y-m-d') }}</td>
                                    <td>{{ $grade->subject->name }}</td>
                                    <td>{{ $grade->teacher->name }}</td>
                                    <td>
                                        @if($grade->grade_type == 'memorization')
                                            <span class="badge bg-primary">حفظ</span>
                                        @elseif($grade->grade_type == 'tajweed')
                                            <span class="badge bg-success">تجويد</span>
                                        @elseif($grade->grade_type == 'recitation')
                                            <span class="badge bg-info">تلاوة</span>
                                        @elseif($grade->grade_type == 'behavior')
                                            <span class="badge bg-warning">سلوك</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($grade->score >= 9)
                                            <span class="badge grade-badge grade-excellent">{{ $grade->score }}/10</span>
                                        @elseif($grade->score >= 7)
                                            <span class="badge grade-badge grade-good">{{ $grade->score }}/10</span>
                                        @elseif($grade->score >= 5)
                                            <span class="badge grade-badge grade-average">{{ $grade->score }}/10</span>
                                        @else
                                            <span class="badge grade-badge grade-poor">{{ $grade->score }}/10</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($grade->notes)
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $grade->notes }}">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد درجات مسجلة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $grades->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grade Distribution Chart -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">توزيع الدرجات</h5>
            </div>
            <div class="card-body">
                <canvas id="gradeDistributionChart" height="300"></canvas>
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
    
    // Grade Type Filter
    document.getElementById('gradeTypeFilter').addEventListener('change', function() {
        const selectedType = this.value;
        const rows = document.querySelectorAll('#gradesTable tbody tr');
        
        rows.forEach(row => {
            if (selectedType === 'all' || row.dataset.gradeType === selectedType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Grade Distribution Chart
    const ctx = document.getElementById('gradeDistributionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['ممتاز (9-10)', 'جيد جدًا (7-8.9)', 'جيد (5-6.9)', 'ضعيف (0-4.9)'],
            datasets: [
                {
                    label: 'الحفظ',
                    backgroundColor: '#4e73df',
                    data: [
                        {{ $grades->where('grade_type', 'memorization')->where('score', '>=', 9)->count() }},
                        {{ $grades->where('grade_type', 'memorization')->where('score', '>=', 7)->where('score', '<', 9)->count() }},
                        {{ $grades->where('grade_type', 'memorization')->where('score', '>=', 5)->where('score', '<', 7)->count() }},
                        {{ $grades->where('grade_type', 'memorization')->where('score', '<', 5)->count() }}
                    ]
                },
                {
                    label: 'التجويد',
                    backgroundColor: '#1cc88a',
                    data: [
                        {{ $grades->where('grade_type', 'tajweed')->where('score', '>=', 9)->count() }},
                        {{ $grades->where('grade_type', 'tajweed')->where('score', '>=', 7)->where('score', '<', 9)->count() }},
                        {{ $grades->where('grade_type', 'tajweed')->where('score', '>=', 5)->where('score', '<', 7)->count() }},
                        {{ $grades->where('grade_type', 'tajweed')->where('score', '<', 5)->count() }}
                    ]
                },
                {
                    label: 'التلاوة',
                    backgroundColor: '#36b9cc',
                    data: [
                        {{ $grades->where('grade_type', 'recitation')->where('score', '>=', 9)->count() }},
                        {{ $grades->where('grade_type', 'recitation')->where('score', '>=', 7)->where('score', '<', 9)->count() }},
                        {{ $grades->where('grade_type', 'recitation')->where('score', '>=', 5)->where('score', '<', 7)->count() }},
                        {{ $grades->where('grade_type', 'recitation')->where('score', '<', 5)->count() }}
                    ]
                },
                {
                    label: 'السلوك',
                    backgroundColor: '#f6c23e',
                    data: [
                        {{ $grades->where('grade_type', 'behavior')->where('score', '>=', 9)->count() }},
                        {{ $grades->where('grade_type', 'behavior')->where('score', '>=', 7)->where('score', '<', 9)->count() }},
                        {{ $grades->where('grade_type', 'behavior')->where('score', '>=', 5)->where('score', '<', 7)->count() }},
                        {{ $grades->where('grade_type', 'behavior')->where('score', '<', 5)->count() }}
                    ]
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: false
                },
                y: {
                    stacked: false,
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
