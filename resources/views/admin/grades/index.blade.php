@extends('layouts.admin')

@section('title', 'سجل التقييمات')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.grades.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle me-1"></i> إضافة تقييم جديد
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Search Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">فلترة النتائج</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.grades.index') }}" method="GET" class="row g-3">
                <!-- Student Filter -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="student_id" class="form-label">الطالب</label>
                        <select class="form-select" id="student_id" name="student_id">
                            <option value="">الكل</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Group Filter -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="group_id" class="form-label">المجموعة</label>
                        <select class="form-select" id="group_id" name="group_id">
                            <option value="">الكل</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Grade Type Filter -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="grade_type" class="form-label">نوع التقييم</label>
                        <select class="form-select" id="grade_type" name="grade_type">
                            <option value="">الكل</option>
                            <option value="achievement" {{ request('grade_type') == 'achievement' ? 'selected' : '' }}>الإنجاز</option>
                            <option value="behavior" {{ request('grade_type') == 'behavior' ? 'selected' : '' }}>السلوك</option>
                            <option value="attendance" {{ request('grade_type') == 'attendance' ? 'selected' : '' }}>الحضور</option>
                            <option value="appearance" {{ request('grade_type') == 'appearance' ? 'selected' : '' }}>المظهر</option>
                        </select>
                    </div>
                </div>

                <!-- Date Range Filter -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_from" class="form-label">من تاريخ</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_to" class="form-label">إلى تاريخ</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.grades.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">متوسط الدرجات</h6>
                    <p class="display-6 mb-0">{{ $stats['average'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">عدد التقييمات</h6>
                    <p class="display-6 mb-0">{{ $stats['count'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="card-title">أعلى درجة</h6>
                    <p class="display-6 mb-0">{{ $stats['highest'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning">
                <div class="card-body text-center">
                    <h6 class="card-title">أدنى درجة</h6>
                    <p class="display-6 mb-0">{{ $stats['lowest'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">إحصائيات حسب نوع التقييم</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Achievement Stats -->
                        <div class="col-md-6">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body">
                                    <h6 class="card-title">الإنجاز</h6>
                                    <p class="mb-1">عدد التقييمات: {{ $stats['by_type']['achievement']['count'] }}</p>
                                    <p class="mb-0">المتوسط: {{ $stats['by_type']['achievement']['average'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Behavior Stats -->
                        <div class="col-md-6">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body">
                                    <h6 class="card-title">السلوك</h6>
                                    <p class="mb-1">عدد التقييمات: {{ $stats['by_type']['behavior']['count'] }}</p>
                                    <p class="mb-0">المتوسط: {{ $stats['by_type']['behavior']['average'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Stats -->
                        <div class="col-md-6">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body">
                                    <h6 class="card-title">الحضور</h6>
                                    <p class="mb-1">عدد التقييمات: {{ $stats['by_type']['attendance']['count'] }}</p>
                                    <p class="mb-0">المتوسط: {{ $stats['by_type']['attendance']['average'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Appearance Stats -->
                        <div class="col-md-6">
                            <div class="card bg-warning h-100">
                                <div class="card-body">
                                    <h6 class="card-title">المظهر</h6>
                                    <p class="mb-1">عدد التقييمات: {{ $stats['by_type']['appearance']['count'] }}</p>
                                    <p class="mb-0">المتوسط: {{ $stats['by_type']['appearance']['average'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">توزيع الدرجات</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">ممتاز (90-100)</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                style="width: {{ $stats['distribution']['excellent']['percentage'] }}%;" 
                                aria-valuenow="{{ $stats['distribution']['excellent']['percentage'] }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                {{ $stats['distribution']['excellent']['percentage'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">جيد جدًا (80-89)</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                style="width: {{ $stats['distribution']['very_good']['percentage'] }}%;" 
                                aria-valuenow="{{ $stats['distribution']['very_good']['percentage'] }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                {{ $stats['distribution']['very_good']['percentage'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">جيد (70-79)</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                style="width: {{ $stats['distribution']['good']['percentage'] }}%;" 
                                aria-valuenow="{{ $stats['distribution']['good']['percentage'] }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                {{ $stats['distribution']['good']['percentage'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مقبول (60-69)</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                style="width: {{ $stats['distribution']['pass']['percentage'] }}%;" 
                                aria-valuenow="{{ $stats['distribution']['pass']['percentage'] }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                {{ $stats['distribution']['pass']['percentage'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ضعيف (أقل من 60)</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" role="progressbar" 
                                style="width: {{ $stats['distribution']['poor']['percentage'] }}%;" 
                                aria-valuenow="{{ $stats['distribution']['poor']['percentage'] }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                {{ $stats['distribution']['poor']['percentage'] }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">قائمة التقييمات</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>الطالب</th>
                            <th>المجموعة</th>
                            <th>الإنجاز</th>
                            <th>السلوك</th>
                            <th>الحضور</th>
                            <th>المظهر</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                     
                        @forelse($grades as $dayGrade)
                      
                        <tr>
                            <td>
                                <a href="{{ route('admin.students.show', $dayGrade['student']->id) }}" class="text-decoration-none">
                                    {{ $dayGrade['student']->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.groups.show', $dayGrade['student']->group_id) }}" class="text-decoration-none">
                                    {{ $dayGrade['student']->group->name }}
                                </a>
                            </td>
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
                            <td>
                                <div class="btn-group" role="group">
                                    @if(isset($dayGrade['grades']))
                                        @php
                                            $firstGrade = collect($dayGrade['grades'])->first();
                                        @endphp
                                        @if($firstGrade)
                                            <a href="{{ route('admin.grades.edit', $firstGrade) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $firstGrade->id }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">لا توجد تقييمات</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $grades->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف هذا التقييم؟
            </div>
            <div class="modal-footer">
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(gradeId) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    form.action = `/admin/grades/${gradeId}`;
    new bootstrap.Modal(modal).show();
}
</script>
@endpush
