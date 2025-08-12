@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4">تفاصيل التسجيل</h2>
        </div>
    </div>

    <div class="row">
        <!-- Enrollment Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات التسجيل</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">الطالب</label>
                            <p>{{ $enrollment->student->name }} </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">المجموعة</label>
                            <p>{{ $enrollment->group->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">الفرع</label>
                            <p>{{ $enrollment->group->branch->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">المعلم</label>
                            <p>{{ $enrollment->group->teacher->name }} </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">تاريخ التسجيل</label>
                            <p>{{ $enrollment->created_at->format('Y-m-d') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">الحالة</label>
                            <p>
                                @if($enrollment->status == 'active')
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Progress -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">تقدم الطالب</h5>
                </div>
                <div class="card-body">
                    <!-- Attendance Summary -->
                    <div class="mb-4">
                        <h6>ملخص الحضور</h6>
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendanceStats['present_percentage'] }}%" aria-valuenow="{{ $attendanceStats['present_percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                حضور ({{ $attendanceStats['present_percentage'] }}%)
                            </div>
                        </div>
                        <div class="small text-muted">
                            إجمالي الحضور: {{ $attendanceStats['total_present'] }} من {{ $attendanceStats['total_sessions'] }} حصة
                        </div>
                    </div>

                    <!-- Grades Summary -->
                    <div>
                        <h6>ملخص الدرجات</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>نوع التقييم</th>
                                        <th>المتوسط</th>
                                        <th>عدد التقييمات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gradeStats as $type => $stat)
                                    <tr>
                                        <td>
                                            @if($type == 'memorization')
                                                <span class="badge bg-primary">حفظ</span>
                                            @elseif($type == 'tajweed')
                                                <span class="badge bg-info">تجويد</span>
                                            @elseif($type == 'recitation')
                                                <span class="badge bg-success">تلاوة</span>
                                            @elseif($type == 'behavior')
                                                <span class="badge bg-warning">سلوك</span>
                                            @endif
                                        </td>
                                        <td>{{ $stat['average'] }}</td>
                                        <td>{{ $stat['count'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">النشاط الأخير</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($recentActivity as $activity)
                            <div class="timeline-item">
                                <div class="timeline-date">{{ $activity->created_at->format('Y-m-d') }}</div>
                                <div class="timeline-content">
                                    @if($activity->type == 'grade')
                                        <span class="badge bg-info">درجة</span>
                                        {{ $activity->details }}
                                    @elseif($activity->type == 'attendance')
                                        <span class="badge bg-success">حضور</span>
                                        {{ $activity->details }}
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">لا يوجد نشاط حديث</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.enrollments.edit', $enrollment->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> تعديل
        </a>
        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> عودة للقائمة
        </a>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 20px;
    margin-bottom: 20px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-date {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 5px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
}
</style>
@endsection
