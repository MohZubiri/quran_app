@extends('layouts.admin')

@section('title', 'قائمة الخطط الدراسية')

@section('actions')
<a href="{{ route('admin.study_plans.create') }}" class="btn btn-sm btn-primary">
    <i class="fas fa-plus me-1"></i> إضافة خطة جديدة
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">جميع الخطط الدراسية</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>رقم الخطة</th>
                    <th> المجموعة</th>
                    <th>عدد الدروس</th>
                    <th>أقل أداء</th>
                    <th>الحالة</th>
                    <th>العمليات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                    <tr>
                        <td>{{ $plan->plan_number }}</td>
                        <td>{{ $plan->group->name }}</td>
                        <td>{{ $plan->lessons_count }}</td>
                        <td>{{ $plan->min_performance }}</td>
                        <td>
                            <span class="badge bg-{{ $plan->status ? 'success' : 'danger' }}">
                                {{ $plan->status ? 'فعال' : 'غير فعال' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.study_plans.show', $plan) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.study_plans.edit', $plan) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.study_plans.destroy', $plan) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('هل أنت متأكد من الحذف؟')" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">لا توجد خطط دراسية</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
