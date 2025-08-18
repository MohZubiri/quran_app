@extends('layouts.admin')

@section('title', 'التقرير الشهري')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">التقرير الشهري للطلاب</h5>
    </div>
    <div class="card-body">
        <!-- نموذج البحث والتصفية -->
        <form action="{{ url('/admin/reports/monthly') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="date_from" class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_to" class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="branch_id" class="form-label">الفرع</label>
                    @if(isset($defaultBranch) && $defaultBranch)
                        <select class="form-select" id="branch_id" disabled>
                            <option value="{{ $defaultBranch->id }}" selected>{{ $defaultBranch->name }}</option>
                        </select>
                        <input type="hidden" name="branch_id" value="{{ $defaultBranch->id }}">
                    @else
                        <select class="form-select" id="branch_id" name="branch_id">
                            <option value="">جميع الفروع</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-md-3 mb-3">
                    <label for="group_id" class="form-label">الحلقة</label>
                    <select class="form-select" id="group_id" name="group_id">
                        <option value="">جميع الحلقات</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> عرض التقرير
                    </button>
                    @if(count($reportData) > 0)
                        <a href="{{ url('/admin/reports/monthly?') . http_build_query(array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-success ms-2">
                            <i class="fas fa-file-excel me-1"></i> تصدير Excel
                        </a>
                        <a href="{{ url('/admin/reports/monthly?') . http_build_query(array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-danger ms-2">
                            <i class="fas fa-file-pdf me-1"></i> تصدير PDF
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if(request('date_from') && request('date_to'))
            @if(count($reportData) > 0)
                <!-- ملخص التقرير -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">ملخص التقرير</h6>
                                <div class="row">
                                    <div class="col-md-2 text-center">
                                        <div class="border-start border-end p-2">
                                            <h6>عدد الطلاب</h6>
                                            <h3>{{ $summary['total_students'] }}</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div class="border-start border-end p-2">
                                            <h6>متوسط الإنجاز</h6>
                                            <h3>{{ number_format($summary['achievement_avg'], 1) }}</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div class="border-start border-end p-2">
                                            <h6>متوسط السلوك</h6>
                                            <h3>{{ number_format($summary['behavior_avg'], 1) }}</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div class="border-start border-end p-2">
                                            <h6>متوسط الحضور</h6>
                                            <h3>{{ number_format($summary['attendance_avg'], 1) }}</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div class="border-start border-end p-2">
                                            <h6>متوسط المظهر</h6>
                                            <h3>{{ number_format($summary['appearance_avg'], 1) }}</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div class="border-start border-end p-2">
                                            <h6>متوسط الإنجاز اليومي</h6>
                                            <h3>{{ number_format($summary['plan_score_avg'], 1) }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- جدول التقرير -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%">#</th>
                                <th style="width: 20%">اسم الطالب</th>
                                <th style="width: 15%">الحلقة</th>
                                <th class="text-center" style="width: 12%">الإنجاز</th>
                                <th class="text-center" style="width: 12%">السلوك</th>
                                <th class="text-center" style="width: 12%">الحضور</th>
                                <th class="text-center" style="width: 12%">المظهر</th>
                                <th class="text-center" style="width: 12%">الإنجاز اليومي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData as $index => $student)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ url('/admin/students/' . $student['id']) }}">{{ $student['name'] }}</a>
                                    </td>
                                    <td>{{ $student['group'] }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $student['achievement'] >= 90 ? 'bg-success' : ($student['achievement'] >= 70 ? 'bg-primary' : ($student['achievement'] >= 50 ? 'bg-warning' : 'bg-danger')) }} fs-6">
                                            {{ $student['achievement'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $student['behavior'] >= 90 ? 'bg-success' : ($student['behavior'] >= 70 ? 'bg-primary' : ($student['behavior'] >= 50 ? 'bg-warning' : 'bg-danger')) }} fs-6">
                                            {{ $student['behavior'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $student['attendance'] >= 90 ? 'bg-success' : ($student['attendance'] >= 70 ? 'bg-primary' : ($student['attendance'] >= 50 ? 'bg-warning' : 'bg-danger')) }} fs-6">
                                            {{ $student['attendance'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $student['appearance'] >= 90 ? 'bg-success' : ($student['appearance'] >= 70 ? 'bg-primary' : ($student['appearance'] >= 50 ? 'bg-warning' : 'bg-danger')) }} fs-6">
                                            {{ $student['appearance'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $student['plan_score'] >= 90 ? 'bg-success' : ($student['plan_score'] >= 70 ? 'bg-primary' : ($student['plan_score'] >= 50 ? 'bg-warning' : 'bg-danger')) }} fs-6">
                                            {{ $student['plan_score'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- رسم بياني للتقرير -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">رسم بياني لمتوسطات التقييمات</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="reportChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> لا توجد بيانات متاحة للفترة المحددة. يرجى تغيير معايير البحث وإعادة المحاولة.
                </div>
            @endif
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i> يرجى تحديد فترة زمنية لعرض التقرير.
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@if(count($reportData) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة الرسم البياني
        const ctx = document.getElementById('reportChart').getContext('2d');
        const reportChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['الإنجاز', 'السلوك', 'الحضور', 'المظهر', 'الإنجاز اليومي'],
                datasets: [{
                    label: 'متوسط التقييمات',
                    data: [
                        {{ $summary['achievement_avg'] }},
                        {{ $summary['behavior_avg'] }},
                        {{ $summary['attendance_avg'] }},
                        {{ $summary['appearance_avg'] }},
                        {{ $summary['plan_score_avg'] }}
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // تحديث قائمة الحلقات عند تغيير الفرع
        document.getElementById('branch_id').addEventListener('change', function() {
            const branchId = this.value;
            const groupSelect = document.getElementById('group_id');
            
            // إعادة تعيين قائمة الحلقات
            groupSelect.innerHTML = '<option value="">جميع الحلقات</option>';
            
            if (branchId) {
                // طلب Ajax للحصول على حلقات الفرع
                fetch(`/admin/branches/${branchId}/groups`)
                    .then(response => response.json())
                    .then(data => {
                        data.groups.forEach(group => {
                            const option = document.createElement('option');
                            option.value = group.id;
                            option.textContent = group.name;
                            groupSelect.appendChild(option);
                        });
                    });
            }
        });
    });
</script>
@endif
@endsection
