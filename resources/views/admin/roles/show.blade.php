@extends('layouts.admin')

@section('title', 'تفاصيل الدور')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل الدور: {{ $role->display_name }}</h1>
        <div>
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary ml-2">
                <i class="fas fa-edit ml-1"></i> تعديل الدور
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right ml-1"></i> العودة للأدوار
            </a>
        </div>
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

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الدور</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold">الاسم:</h6>
                        <p>{{ $role->name }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">الاسم المعروض:</h6>
                        <p>{{ $role->display_name }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">الوصف:</h6>
                        <p>{{ $role->description ?: 'لا يوجد وصف' }}</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">عدد المستخدمين:</h6>
                        <p><span class="badge badge-primary">{{ $role->users->count() }}</span></p>
                    </div>
                    <div class="mb-3">
                        <h6 class="font-weight-bold">عدد الصلاحيات:</h6>
                        <p><span class="badge badge-info">{{ $role->permissions->count() }}</span></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الصلاحيات</h6>
                </div>
                <div class="card-body">
                    @forelse($permissionsByGroup as $group => $permissions)
                        <div class="mb-3">
                            <h6 class="border-right-primary pr-2 mb-2">{{ $group }}</h6>
                            <div class="row">
                                @foreach($permissions as $permission)
                                    <div class="col-md-4 mb-2">
                                        <span class="badge badge-success p-2">
                                            <i class="fas fa-check-circle ml-1"></i>
                                            {{ $permission->display_name }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">لا توجد صلاحيات مضافة لهذا الدور</p>
                    @endforelse
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">المستخدمون بهذا الدور</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#assignUsersModal">
                        <i class="fas fa-user-plus ml-1"></i> إضافة مستخدمين
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('admin.users.permissions', $user) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-key"></i>
                                                </a>
                                                <form action="{{ route('admin.roles.remove-user', [$role, $user]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من إزالة هذا المستخدم من الدور؟')">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">لا يوجد مستخدمون بهذا الدور حتى الآن</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Users Modal -->
<div class="modal fade" id="assignUsersModal" tabindex="-1" role="dialog" aria-labelledby="assignUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignUsersModalLabel">إضافة مستخدمين للدور: {{ $role->display_name }}</h5>
                <button type="button" class="close ml-0 mr-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.roles.assign-users', $role) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="users">اختر المستخدمين</label>
                        <select class="form-control select2" id="users" name="users[]" multiple>
                            @foreach(\App\Models\User::whereDoesntHave('roles', function($query) use ($role) {
                                $query->where('id', $role->id);
                            })->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة المستخدمين</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'اختر المستخدمين',
            dir: 'rtl',
            language: 'ar'
        });
    });
</script>
@endsection
