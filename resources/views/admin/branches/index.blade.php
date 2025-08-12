@extends('layouts.admin')

@section('title', 'إدارة الفروع')

@section('actions')
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">قائمة الفروع</h5>
        @can('create-branches')
        <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة فرع جديد
        </a>
        @endcan
    </div>
    <div class="card-body">
        @if(count($branches) > 0)
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الفرع</th>
                        <th>العنوان</th>
                        <th>رقم الهاتف</th>
                        <th>البريد الإلكتروني</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branches as $branch)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $branch->name }}</td>
                        <td>{{ $branch->address ?? 'غير محدد' }}</td>
                        <td>{{ ($branch->phone==null)? $branch->manager_phone :'غير محدد' }}</td>
                        <td>{{ $branch->email ?? 'غير محدد' }}</td>
                        <td>
                            @can('view-branches')
                            <a href="{{ route('admin.branches.show', $branch->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan
                            
                            @can('edit-branches')
                            <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            
                            @can('delete-branches')
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $branch->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endcan
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $branch->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $branch->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $branch->id }}">تأكيد الحذف</h5>
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
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-info">
            لا توجد فروع مضافة حتى الآن. @can('create-branches')<a href="{{ route('admin.branches.create') }}">إضافة فرع جديد</a>@endcan
        </div>
        @endif
    </div>
</div>
@endsection
