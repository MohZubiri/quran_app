@extends('layouts.admin')

@section('title', 'تفاصيل الخطة الدراسية')

@section('actions')
<a href="{{ route('admin.study_plans.index') }}" class="btn btn-sm btn-secondary">
    <i class="fas fa-arrow-right me-1"></i> العودة إلى قائمة الخطط الدراسية
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">تفاصيل الخطة</h5>
    </div>
    <div class="card-body">
        <ul class="list-group">
            <li class="list-group-item"><strong>رقم الخطة:</strong> {{ $study_plan->plan_number }}</li>
            <li class="list-group-item"><strong>رقم المجموعة:</strong> {{ $study_plan->group_number }}</li>
            <li class="list-group-item"><strong>عدد الدروس:</strong> {{ $study_plan->lessons_count }}</li>
            <li class="list-group-item"><strong>أقل أداء:</strong> {{ $study_plan->min_performance }}%</li>
            <li class="list-group-item"><strong>الحالة:</strong> {{ $study_plan->status ? 'فعال' : 'غير فعال' }}</li>
        </ul>
    </div>
</div>
@endsection
