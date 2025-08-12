@extends('layouts.admin')

@section('title', 'إدارة المعلمين')

@section('actions')
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">قائمة المعلمين</h5>
        @can('create-teachers')
        <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة معلم جديد
        </a>
        @endcan
    </div>
    <div class="card-body">
        @if(count($teachers) > 0)
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الهاتف</th>
                        <th>التخصص</th>
                        <th>الفرع</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $teacher->name }}</td>
                        <td>{{ $teacher->email?? 'غير محدد' }}</td>
                        <td>{{ $teacher->phone ?? 'غير محدد' }}</td>
                        <td>{{ $teacher->specialization ?? 'غير محدد' }}</td>
                        <td>{{ $teacher->branch->name ?? 'غير محدد' }}</td>
                        <td>
                            @if($teacher->status == 'active')
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">غير نشط</span>
                            @endif
                        </td>
                        <td>
                            @can('view-teachers')
                            <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan
                            
                            @can('edit-teachers')
                            <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            
                            @can('delete-teachers')
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $teacher->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endcan
                            
                            <!-- ربط المستخدم بالمعلم -->
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#assignUserModal{{ $teacher->id }}">
                                <i class="fas fa-user-plus"></i>
                            </button>
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $teacher->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $teacher->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $teacher->id }}">تأكيد الحذف</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                        </div>
                                        <div class="modal-body">
                                            هل أنت متأكد من رغبتك في حذف المعلم "{{ $teacher->name }} "؟ هذا الإجراء لا يمكن التراجع عنه.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">حذف</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Assign User Modal -->
                            <div class="modal fade" id="assignUserModal{{ $teacher->id }}" tabindex="-1" aria-labelledby="assignUserModalLabel{{ $teacher->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="assignUserModalLabel{{ $teacher->id }}">ربط المستخدم بالمعلم</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Assign User Form -->
                                            <form action="{{ route('admin.teachers.assign-user', $teacher->id) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="user_id" class="form-label">المستخدم</label>
                                                    <select name="user_id" id="user_id" class="form-select">
                                                        @foreach($teacherUsers as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary">ربط المستخدم</button>
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
        @else
        <div class="alert alert-info">
            لا يوجد معلمين مضافين حتى الآن. <a href="{{ route('admin.teachers.create') }}">إضافة معلم جديد</a>
        </div>
        @endif
    </div>
</div>
@endsection
