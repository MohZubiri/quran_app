@extends('layouts.admin')

@section('title', 'إدارة الصلاحيات')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة الصلاحيات</h1>
        @can('create-permissions')
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle ml-1"></i> إضافة صلاحية جديدة
        </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الصلاحيات</h6>
            @can('create-permissions')
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة صلاحية جديدة
            </a>
            @endcan
        </div>
        <div class="card-body">
            @forelse($permissionsByGroup as $group => $permissions)
                <div class="mb-4">
                    <h5 class="border-right-primary pr-2 mb-3">{{ $group }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>الاسم المعروض</th>
                                    <th>الوصف</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->display_name }}</td>
                                        <td>{{ $permission->description }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                @can('view-permissions')
                                                <a href="{{ route('admin.permissions.show', $permission->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                
                                                @can('edit-permissions')
                                                <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                
                                                @can('delete-permissions')
                                                <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذه الصلاحية؟')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <p class="text-muted mb-0">لا توجد صلاحيات مضافة حتى الآن</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
