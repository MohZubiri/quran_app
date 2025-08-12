@extends('layouts.admin')

@section('title', 'إدارة المواد الدراسية')

@section('actions')
    @can('create-subjects')
    <a href="{{ route('admin.subjects.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus-circle me-1"></i> إضافة مادة جديدة
    </a>
    @endcan
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title">قائمة المواد الدراسية</h5>
        @can('create-subjects')
        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة مادة جديدة
        </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('admin.subjects.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="branch_id" class="form-select">
                        <option value="">-- جميع الفروع --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="بحث باسم المادة" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>

        @if(count($subjects) > 0)
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المادة</th>
                        <th>الوصف</th>
                        <th>الفرع</th>
                        <th>عدد الحلقات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $subject)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $subject->name }}</td>
                        <td>{{ Str::limit($subject->description, 50) ?? 'لا يوجد وصف' }}</td>
                        <td>{{ $subject->branch->name ?? 'غير محدد' }}</td>
                        <td>{{ $subject->groups->count() }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('view-subjects')
                                <a href="{{ route('admin.subjects.show', $subject->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                
                                @can('edit-subjects')
                                <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                
                                @can('delete-subjects')
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $subject->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endcan
                            </div>
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $subject->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $subject->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $subject->id }}">تأكيد الحذف</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                        </div>
                                        <div class="modal-body">
                                            هل أنت متأكد من رغبتك في حذف المادة "{{ $subject->name }}"؟ هذا الإجراء لا يمكن التراجع عنه.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="d-inline">
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
            {{ $subjects->links() }}
        </div>
        @else
        <div class="alert alert-info">
            لا توجد مواد دراسية مضافة حتى الآن. <a href="{{ route('admin.subjects.create') }}">إضافة مادة جديدة</a>
        </div>
        @endif
    </div>
</div>
@endsection
