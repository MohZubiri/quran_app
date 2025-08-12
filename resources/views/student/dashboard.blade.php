@extends('layouts.student')

@section('title', 'لوحة التحكم')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">لوحة التحكم</h1>
    </div>
</div>

<div class="row">
    <!-- Student Info Card -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-gradient-primary">
                <h5 class="mb-0">معلومات الطالب</h5>
            </div>
            <div class="card-body text-center">
                <div class="avatar-circle">
                    <i class="fas fa-user"></i>
                </div>
                <h4>{{ $student->name }}</h4>
                <p class="text-muted">{{ $student->group->name ?? 'غير مسجل في مجموعة' }}</p>
                <p class="text-muted">{{ $student->branch->name ?? 'غير مسجل في فرع' }}</p>
                <div class="mt-3">
                    <p><i class="fas fa-calendar-alt me-2"></i> تاريخ الانضمام: {{ $student->created_at->format('Y-m-d') }}</p>
                    <p><i class="fas fa-book me-2"></i> المستوى الحالي: {{ $student->current_level }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Attendance Card -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-gradient-info">
                <h5 class="mb-0">سجل الحضور</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>المجموعة</th>
                                <th>المعلم</th>
                                <th>الملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendance as $record)
                                <tr>
                                    <td>{{ $record->date->format('Y-m-d') }}</td>
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
                                    <td>{{ $record->group->name }}</td>
                                    <td>{{ $record->teacher->name }}</td>
                                    <td>{{ $record->notes }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد سجلات حضور</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Grades Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">قائمة التقييمات</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>المجموعة</th>
                                <th>الإنجاز</th>
                                <th>السلوك</th>
                                <th>الحضور</th>
                                <th>المظهر</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentGrades as $dayGrade)
                            <tr>
                                <td>{{ $student->group->name }}</td>
                                <td>
                                    @if(isset($dayGrade['grades']['achievement']))
                                        @if($dayGrade['grades']['achievement']->grade >= 90)
                                            <span class="badge bg-success">{{ $dayGrade['grades']['achievement']->grade }}</span>
                                        @elseif($dayGrade['grades']['achievement']->grade >= 80)
                                            <span class="badge bg-info">{{ $dayGrade['grades']['achievement']->grade }}</span>
                                        @elseif($dayGrade['grades']['achievement']->grade >= 70)
                                            <span class="badge bg-primary">{{ $dayGrade['grades']['achievement']->grade }}</span>
                                        @elseif($dayGrade['grades']['achievement']->grade >= 60)
                                            <span class="badge bg-warning">{{ $dayGrade['grades']['achievement']->grade }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $dayGrade['grades']['achievement']->grade }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($dayGrade['grades']['behavior']))
                                        @if($dayGrade['grades']['behavior']->grade >= 90)
                                            <span class="badge bg-success">{{ $dayGrade['grades']['behavior']->grade }}</span>
                                        @elseif($dayGrade['grades']['behavior']->grade >= 80)
                                            <span class="badge bg-info">{{ $dayGrade['grades']['behavior']->grade }}</span>
                                        @elseif($dayGrade['grades']['behavior']->grade >= 70)
                                            <span class="badge bg-primary">{{ $dayGrade['grades']['behavior']->grade }}</span>
                                        @elseif($dayGrade['grades']['behavior']->grade >= 60)
                                            <span class="badge bg-warning">{{ $dayGrade['grades']['behavior']->grade }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $dayGrade['grades']['behavior']->grade }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($dayGrade['grades']['attendance']))
                                        @if($dayGrade['grades']['attendance']->grade >= 90)
                                            <span class="badge bg-success">{{ $dayGrade['grades']['attendance']->grade }}</span>
                                        @elseif($dayGrade['grades']['attendance']->grade >= 80)
                                            <span class="badge bg-info">{{ $dayGrade['grades']['attendance']->grade }}</span>
                                        @elseif($dayGrade['grades']['attendance']->grade >= 70)
                                            <span class="badge bg-primary">{{ $dayGrade['grades']['attendance']->grade }}</span>
                                        @elseif($dayGrade['grades']['attendance']->grade >= 60)
                                            <span class="badge bg-warning">{{ $dayGrade['grades']['attendance']->grade }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $dayGrade['grades']['attendance']->grade }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($dayGrade['grades']['appearance']))
                                        @if($dayGrade['grades']['appearance']->grade >= 90)
                                            <span class="badge bg-success">{{ $dayGrade['grades']['appearance']->grade }}</span>
                                        @elseif($dayGrade['grades']['appearance']->grade >= 80)
                                            <span class="badge bg-info">{{ $dayGrade['grades']['appearance']->grade }}</span>
                                        @elseif($dayGrade['grades']['appearance']->grade >= 70)
                                            <span class="badge bg-primary">{{ $dayGrade['grades']['appearance']->grade }}</span>
                                        @elseif($dayGrade['grades']['appearance']->grade >= 60)
                                            <span class="badge bg-warning">{{ $dayGrade['grades']['appearance']->grade }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $dayGrade['grades']['appearance']->grade }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $dayGrade['date'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">لا توجد تقييمات</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $recentGrades->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
