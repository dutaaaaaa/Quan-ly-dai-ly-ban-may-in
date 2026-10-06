@extends('layouts.app')
@section('content')
<div class="container mt-5" style="min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white text-center border-0 pt-4 pb-0">
                    <h3 class="font-weight-bold text-info mb-0">ĐĂNG NHẬP</h3>
                    <p class="text-secondary mt-2">Chào mừng bạn quay lại Web An Tâm</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    {{-- Hiển thị thông báo lỗi nếu sai tài khoản/mật khẩu --}}
                    @if ($errors->any())
                        <div class="alert alert-danger py-2 text-center" style="border-radius: 10px;">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        {{-- ĐÂY CHÍNH LÀ DÒNG LỆNH KHẮC PHỤC LỖI 419 --}}
                        @csrf 

                        <div class="form-group mb-3">
                            <label for="email" class="font-weight-bold">Địa chỉ Email</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg bg-light border-0" placeholder="Nhập email của bạn..." required autofocus>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="password" class="font-weight-bold">Mật khẩu</label>
                            <input type="password" name="password" id="password" class="form-control form-control-lg bg-light border-0" placeholder="Nhập mật khẩu..." required>
                        </div>
                        
                        <button type="submit" class="btn btn-info btn-lg w-100 text-white font-weight-bold mb-3" style="border-radius: 10px; box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);">
                            ĐĂNG NHẬP
                        </button>
                        
                        <div class="text-center mt-3">
                            <p class="mb-0 text-secondary">
                                Chưa có tài khoản? 
                                <a href="{{ route('register') }}" class="text-info font-weight-bold" style="text-decoration: none;">Đăng ký ngay</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection