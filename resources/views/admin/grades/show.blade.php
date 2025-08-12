@extends('layouts.admin')

@section('title', 'تفاصيل التقييم')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.grades.edit', $grade->id) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit me-1"></i> تعديل التقييم
    </a>
    <a href="{{ route('admin.grades.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى سجل التقييمات
    </a>
    @if($grade->group)
    <a href="{{ route('admin.groups.show', $grade->group_id) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى الحلقة
    </a>
    @endif
    @if($grade->student)
    <a href="{{ route('admin.students.show', $grade->student_id) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى الطالب
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">تفاصيل التقييم</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">الطالب:</h6>
                        <p class="mb-0">
                            <a href="{{ route('admin.students.show', $grade->student_id) }}">
                                {{ $grade->student->name }} 
                            </a>
                        </p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold">المعلم المقيّم:</h6>
                        <p class="mb-0">
                            <a href="{{ route('admin.teachers.show', $grade->teacher_id) }}">
                                {{ $grade->teacher->name }} 
                            </a>
                        </p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold">الحلقة:</h6>
                        <p class="mb-0">
                            <a href="{{ route('admin.groups.show', $grade->group_id) }}">
                                {{ $grade->group->name }}
                            </a>
                        </p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold">المادة:</h6>
                        <p class="mb-0">
                            <a href="{{ route('admin.subjects.show', $grade->subject_id) }}">
                                {{ $grade->subject->name }}
                            </a>
                        </p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold">تاريخ التقييم:</h6>
                        <p class="mb-0">{{ $grade->date->format('Y-m-d') }}</p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold">نوع التقييم:</h6>
                        <p class="mb-0">
                            @if($grade->grade_type == 'memorization')
                                <span class="badge bg-primary">حفظ</span>
                            @elseif($grade->grade_type == 'tajweed')
                                <span class="badge bg-info">تجويد</span>
                            @elseif($grade->grade_type == 'recitation')
                                <span class="badge bg-success">تلاوة</span>
                            @elseif($grade->grade_type == 'behavior')
                                <span class="badge bg-warning">سلوك</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold">الدرجة:</h6>
                        <p class="mb-0">
                            <span class="badge {{ $grade->grade >= 90 ? 'bg-success' : ($grade->grade >= 70 ? 'bg-info' : ($grade->grade >= 50 ? 'bg-warning' : 'bg-danger')) }} fs-6">
                                {{ $grade->grade }}
                            </span>
                            /100
                        </p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold">التقدير:</h6>
                        <p class="mb-0">
                            @if($grade->grade >= 90)
                                <span class="text-success fw-bold">ممتاز</span>
                            @elseif($grade->grade >= 80)
                                <span class="text-info fw-bold">جيد جدًا</span>
                            @elseif($grade->grade >= 70)
                                <span class="text-primary fw-bold">جيد</span>
                            @elseif($grade->grade >= 60)
                                <span class="text-warning fw-bold">مقبول</span>
                            @else
                                <span class="text-danger fw-bold">ضعيف</span>
                            @endif
                        </p>
                    </div>
                    
                    @if($grade->verses_covered)
                    <div class="col-12">
                        <h6 class="fw-bold">الآيات التي تم تقييمها:</h6>
                        <p class="mb-0">{{ $grade->verses_covered }}</p>
                    </div>
                    @endif
                    
                    @if($grade->notes)
                    <div class="col-12">
                        <h6 class="fw-bold">ملاحظات التقييم:</h6>
                        <p class="mb-0">{{ $grade->notes }}</p>
                    </div>
                    @endif
                </div>
                
                <div class="mt-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">معايير التقييم</h6>
                        </div>
                        <div class="card-body">
                            @if($grade->grade_type == 'memorization')
                                <h6 class="text-primary">معايير تقييم الحفظ:</h6>
                                <ul>
                                    <li>إتقان الحفظ بدون أخطاء (40 درجة)</li>
                                    <li>الطلاقة في الاسترجاع (30 درجة)</li>
                                    <li>الالتزام بالتجويد أثناء الحفظ (20 درجة)</li>
                                    <li>فهم معاني الآيات المحفوظة (10 درجات)</li>
                                </ul>
                            @elseif($grade->grade_type == 'tajweed')
                                <h6 class="text-info">معايير تقييم التجويد:</h6>
                                <ul>
                                    <li>مخارج الحروف (25 درجة)</li>
                                    <li>أحكام النون الساكنة والتنوين (25 درجة)</li>
                                    <li>أحكام المدود (25 درجة)</li>
                                    <li>الوقف والابتداء (15 درجة)</li>
                                    <li>الترتيل وحسن الأداء (10 درجات)</li>
                                </ul>
                            @elseif($grade->grade_type == 'recitation')
                                <h6 class="text-success">معايير تقييم التلاوة:</h6>
                                <ul>
                                    <li>صحة القراءة (30 درجة)</li>
                                    <li>الالتزام بأحكام التجويد (30 درجة)</li>
                                    <li>الترتيل وحسن الصوت (20 درجة)</li>
                                    <li>الطلاقة وعدم التردد (10 درجات)</li>
                                    <li>الخشوع والتدبر (10 درجات)</li>
                                </ul>
                            @elseif($grade->grade_type == 'behavior')
                                <h6 class="text-warning">معايير تقييم السلوك:</h6>
                                <ul>
                                    <li>الالتزام بآداب المجلس (25 درجة)</li>
                                    <li>احترام المعلم والزملاء (25 درجة)</li>
                                    <li>المشاركة الإيجابية (20 درجة)</li>
                                    <li>الانضباط والالتزام بالمواعيد (20 درجة)</li>
                                    <li>المظهر العام والنظافة (10 درجات)</li>
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">سجل تقييمات الطالب</h5>
            </div>
            <div class="card-body">
                @if($studentGrades->count() > 0)
                <div class="list-group">
                    @foreach($studentGrades as $studentGrade)
                    <a href="{{ route('admin.grades.show', $studentGrade->id) }}" class="list-group-item list-group-item-action {{ $studentGrade->id == $grade->id ? 'active' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge {{ $studentGrade->grade_type == 'memorization' ? 'bg-primary' : ($studentGrade->grade_type == 'tajweed' ? 'bg-info' : ($studentGrade->grade_type == 'recitation' ? 'bg-success' : 'bg-warning')) }}">
                                    @if($studentGrade->grade_type == 'memorization')
                                        حفظ
                                    @elseif($studentGrade->grade_type == 'tajweed')
                                        تجويد
                                    @elseif($studentGrade->grade_type == 'recitation')
                                        تلاوة
                                    @elseif($studentGrade->grade_type == 'behavior')
                                        سلوك
                                    @endif
                                </span>
                                <small class="ms-2">{{ $studentGrade->date->format('Y-m-d') }}</small>
                            </div>
                            <span class="badge {{ $studentGrade->grade >= 90 ? 'bg-success' : ($studentGrade->grade >= 70 ? 'bg-info' : ($studentGrade->grade >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $studentGrade->grade }}
                            </span>
                        </div>
                        <small class="d-block mt-1">{{ $studentGrade->subject->name }}</small>
                        @if($studentGrade->verses_covered)
                            <small class="d-block">{{ Str::limit($studentGrade->verses_covered, 30) }}</small>
                        @endif
                    </a>
                    @endforeach
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('admin.grades.index', ['student_id' => $grade->student_id]) }}" class="btn btn-sm btn-outline-primary w-100">
                        عرض كل تقييمات الطالب
                    </a>
                </div>
                @else
                <div class="alert alert-info mb-0">
                    لا توجد تقييمات أخرى لهذا الطالب.
                </div>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">إحصائيات الطالب</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-bold">متوسط الدرجات:</h6>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar {{ $studentStats['average'] >= 90 ? 'bg-success' : ($studentStats['average'] >= 70 ? 'bg-info' : ($studentStats['average'] >= 50 ? 'bg-warning' : 'bg-danger')) }}" role="progressbar" style="width: {{ $studentStats['average'] }}%;" aria-valuenow="{{ $studentStats['average'] }}" aria-valuemin="0" aria-valuemax="100">{{ $studentStats['average'] }}</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold">عدد التقييمات:</h6>
                    <p>{{ $studentStats['count'] }} تقييم</p>
                </div>
                
                <div>
                    <h6 class="fw-bold">التوزيع حسب النوع:</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-primary">حفظ</span>
                        <span>{{ $studentStats['by_type']['memorization'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-info">تجويد</span>
                        <span>{{ $studentStats['by_type']['tajweed'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-success">تلاوة</span>
                        <span>{{ $studentStats['by_type']['recitation'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="badge bg-warning">سلوك</span>
                        <span>{{ $studentStats['by_type']['behavior'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-between">
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
        <i class="fas fa-trash me-1"></i> حذف التقييم
    </button>
    <a href="{{ route('admin.grades.edit', $grade->id) }}" class="btn btn-warning">
        <i class="fas fa-edit me-1"></i> تعديل التقييم
    </a>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف تقييم الطالب "{{ $grade->student->name }}" بتاريخ {{ $grade->date->format('Y-m-d') }}؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.grades.destroy', $grade->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
