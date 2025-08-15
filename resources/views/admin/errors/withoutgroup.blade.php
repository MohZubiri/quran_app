@extends('layouts.admin')

@section('title', 'لا توجد حلقة دراسية')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-circle text-warning" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="mb-3">مرحباً {{ Auth::user()->name }}</h3>
                    <div class="alert alert-warning">
                        <h5>أنت حالياً لا تملك حلقة دراسية</h5>
                        <p class="mb-0">يرجى التواصل مع المشرف لتعيينك في حلقة دراسية</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('logout') }}" class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-1"></i> تسجيل الخروج
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection