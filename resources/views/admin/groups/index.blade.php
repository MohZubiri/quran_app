@extends('layouts.admin')

@section('title', 'إدارة الحلقات')

@section('actions')
@can('create-groups')
<a href="{{ route('admin.groups.create') }}" class="btn btn-sm btn-primary">
    <i class="fas fa-plus-circle me-1"></i> إضافة حلقة جديدة
</a>
@endcan
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">قائمة الحلقات</h5>
        @can('create-groups')
        <a href="{{ route('admin.groups.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة حلقة جديدة
        </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('admin.groups.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="branch_id" class="form-select">
                        <option value="">-- جميع الفروع --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="subject_id" class="form-select">
                        <option value="">-- جميع المواد --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="teacher_id" class="form-select">
                        <option value="">-- جميع المعلمين --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="بحث باسم الحلقة" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>

        @if(count($groups) > 0)
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الحلقة</th>
                        <th>المادة</th>
                        <th>المعلم</th>
                        <th>الفرع</th>
                        <th>الجدول</th>
                        <th>عدد الطلاب</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $group)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->subject->name ?? 'غير محدد' }}</td>
                        <td>{{ $group->teacher->name ?? '' }} </td>
                        <td>{{ $group->branch->name ?? 'غير محدد' }}</td>
                        <td>{{ $group->schedule ?? 'غير محدد' }}</td>
                        <td>{{ $group->enrollments->count() }}</td>
                        <td>
                            @if($group->status == 'active')
                                <span class="badge bg-success">نشط</span>
                            @elseif($group->status == 'completed')
                                <span class="badge bg-info">مكتمل</span>
                            @elseif($group->status == 'cancelled')
                                <span class="badge bg-danger">ملغي</span>
                            @else
                                <span class="badge bg-secondary">{{ $group->status }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('view-groups')
                                <a href="{{ route('admin.groups.show', $group->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                
                                @can('edit-groups')
                                <a href="{{ route('admin.groups.edit', $group->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                
                                @can('delete-groups')
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $group->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endcan
                            </div>
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $group->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $group->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $group->id }}">تأكيد الحذف</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                        </div>
                                        <div class="modal-body">
                                            هل أنت متأكد من رغبتك في حذف الحلقة "{{ $group->name }}"؟ هذا الإجراء لا يمكن التراجع عنه.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <form action="{{ route('admin.groups.destroy', $group->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">حذف</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $groups->links() }}
        </div>
        @else
        <div class="alert alert-info">
            لا توجد حلقات مضافة حتى الآن. <a href="{{ route('admin.groups.create') }}">إضافة حلقة جديدة</a>
        </div>
        @endif
    </div>
</div>
@endsection
