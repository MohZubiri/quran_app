@extends('layouts.admin')

@section('title', 'تفاصيل الفرع')

@section('actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.branches.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-right me-1"></i> العودة إلى قائمة الفروع
    </a>
    <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit me-1"></i> تعديل الفرع
    </a>
    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
        <i class="fas fa-trash me-1"></i> حذف الفرع
    </button>
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
                هل أنت متأكد من رغبتك في حذف فرع "{{ $branch->name }}"؟ هذا الإجراء لا يمكن التراجع عنه.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">معلومات الفرع</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 30%">اسم الفرع</th>
                            <td>{{ $branch->name }}</td>
                        </tr>
                        <tr>
                            <th>العنوان</th>
                            <td>{{ $branch->address ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>رقم الهاتف</th>
                            <td>{{ $branch->phone ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>البريد الإلكتروني</th>
                            <td>{{ $branch->email ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء</th>
                            <td>{{ $branch->created_at->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th>آخر تحديث</th>
                            <td>{{ $branch->updated_at->format('Y-m-d') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">إحصائيات الفرع</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">الطلاب</h5>
                                <p class="card-text display-6">{{ $branch->students->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">المعلمين</h5>
                                <p class="card-text display-6">{{ $branch->teachers->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">المواد</h5>
                                <p class="card-text display-6">{{ $branch->subjects->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">الحلقات</h5>
                                <p class="card-text display-6">{{ $branch->groups->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">الحلقات في هذا الفرع</h5>
                <a href="{{ route('admin.groups.create', ['branch_id' => $branch->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> إضافة حلقة جديدة
                </a>
            </div>
            <div class="card-body">
                @if($branch->groups->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الحلقة</th>
                                <th>المادة</th>
                                <th>المعلم</th>
                                <th>الجدول</th>
                                <th>عدد الطلاب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($branch->groups as $group)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $group->name ?? 'حلقة ' . $group->id }}</td>
                                <td>{{ $group->subject->name ?? 'غير محدد' }}</td>
                                <td>{{ $group->teacher->full_name ?? 'غير محدد' }}</td>
                                <td>{{ $group->schedule ?? 'غير محدد' }}</td>
                                <td>{{ $group->enrollments->count() }} / {{ $group->max_students }}</td>
                                <td>
                                    <a href="{{ route('admin.groups.show', $group->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    لا توجد حلقات في هذا الفرع حتى الآن.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
