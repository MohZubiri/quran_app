@extends('layouts.admin')

@section('title', 'عرض بيانات المستخدم')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">بيانات المستخدم: {{ $user->name }}</h1>
        <div>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit ml-1"></i> تعديل
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right ml-1"></i> العودة
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">المعلومات الأساسية</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">الاسم</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>البريد الإلكتروني</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>رقم الهاتف</th>
                                <td>{{ $user->phone ?: 'غير متوفر' }}</td>
                            </tr>
                            <tr>
                                <th>نوع المستخدم</th>
                                <td>
                                @if($user->roles->first())
                                        <span class="badge badge-primary">{{ $user->roles->first()->display_name }}</span>
                                    @else
                                        <span class="badge badge-secondary">غير محدد</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>تاريخ الإنشاء</th>
                                <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>آخر تحديث</th>
                                <td>{{ $user->updated_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الأدوار والصلاحيات</h6>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">الأدوار</h5>
                    @if($user->roles->count() > 0)
                        <div class="mb-4">
                            @foreach($user->roles as $role)
                                <span class="badge badge-primary p-2 m-1">{{ $role->display_name ?: $role->name }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">لا توجد أدوار مخصصة لهذا المستخدم</p>
                    @endif

                    <hr>

                    <h5 class="mb-3">الصلاحيات المباشرة</h5>
                    @if($user->permissions->count() > 0)
                        <div>
                            @foreach($user->permissions as $permission)
                                <span class="badge badge-info p-2 m-1">{{ $permission->display_name ?: $permission->name }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">لا توجد صلاحيات مباشرة مخصصة لهذا المستخدم</p>
                    @endif

                    @if(auth()->user()->hasPermissionTo('manage-user-permissions'))
                        <div class="mt-4">
                            <a href="{{ route('admin.users.permissions', $user) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-key ml-1"></i> إدارة الصلاحيات
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
