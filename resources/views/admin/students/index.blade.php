@extends('layouts.admin')

@section('title', 'إدارة الطلاب')

@section('actions')
    @can('create-students')
    <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus-circle me-1"></i> إضافة طالب جديد
    </a>
    @endcan
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">قائمة الطلاب</h5>
        @can('create-students')
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة طالب جديد
        </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('admin.students.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="branch_id" class="form-select">
                        <option value="">-- جميع الفروع --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو رقم الواتس" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>

        @if(count($students) > 0)
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>تاريخ الميلاد</th>
                        <th>رقم الواتس</th>
                        <th>رقم ولي الأمر</th>
                        <th>الفرع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->birth_date ? $student->birth_date->format('Y-m-d') : 'غير محدد' }}</td>
                        <td>{{ $student->phone ?? 'غير محدد' }}</td>
                        <td>{{ $student->parent_phone ?? 'غير محدد' }}</td>
                        <td>{{ $student->branch->name ?? 'غير محدد' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('view-students')
                                <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                
                                @can('edit-students')
                                <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                
                                @can('delete-students')
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $student->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endcan
                            </div>
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $student->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $student->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $student->id }}">تأكيد الحذف</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                        </div>
                                        <div class="modal-body">
                                            هل أنت متأكد من رغبتك في حذف الطالب "{{ $student->name }}"؟ هذا الإجراء لا يمكن التراجع عنه.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="d-inline">
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
            {{ $students->links() }}
        </div>
        @else
        <div class="alert alert-info">
            لا يوجد طلاب مضافين حتى الآن. <a href="{{ route('admin.students.create') }}">إضافة طالب جديد</a>
        </div>
        @endif
    </div>
</div>
@endsection
