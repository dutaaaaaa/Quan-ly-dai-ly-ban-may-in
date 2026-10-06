@extends('layouts.app')
@section('content')
<div class="container mt-5" style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
    <div class="card shadow-sm border-0" style="width: 100%; max-width: 500px; border-radius: 15px;">
        <div class="card-body p-5 text-center">
            <h3 class="font-weight-bold text-info mb-3">XÁC THỰC EMAIL</h3>
            
            <p class="mb-4 text-secondary">
                Vui lòng kiểm tra hộp thư email của bạn (bao gồm cả thư rác/spam) và click vào đường link xác thực để kích hoạt tài khoản.
            </p>

            @if (session('message'))
                <div class="alert alert-success text-center">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px;">
                    Gửi lại email xác thực
                </button>
            </form>
        </div>
    </div>
</div>
@endsection