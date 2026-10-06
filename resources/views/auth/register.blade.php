@extends('layouts.app')
@section('content')
<div class="container" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 40px 0;">
    <div class="card border-0" style="width: 100%; max-width: 500px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden;">
        
        <div class="card-header border-0 text-center text-white" style="background: linear-gradient(135deg, #0284c7, #06b6d4); padding: 35px 20px 25px;">
            <h4 class="mb-0 font-weight-bold" style="letter-spacing: 1px;">TẠO TÀI KHOẢN MỚI</h4>
        </div>
        
        <div class="card-body p-4 p-md-5 bg-white">
            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="form-group mb-4">
                    <label class="font-weight-bold text-secondary mb-2" style="font-size: 0.85rem;">HỌ VÀ TÊN</label>
                    <input type="text" name="name" class="form-control bg-light border-0 px-4" placeholder="Ví dụ: Trần Đức Thắng" style="border-radius: 12px; padding: 12px 15px;" required>
                </div>
                
                <div class="form-group mb-4">
                    <label class="font-weight-bold text-secondary mb-2" style="font-size: 0.85rem;">ĐỊA CHỈ EMAIL</label>
                    <input type="email" name="email" class="form-control bg-light border-0 px-4" placeholder="Nhập email thật để nhận OTP..." style="border-radius: 12px; padding: 12px 15px;" required>
                    @error('email') <span class="text-danger d-block mt-2" style="font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group mb-4">
                    <label class="font-weight-bold text-secondary mb-2" style="font-size: 0.85rem;">MẬT KHẨU</label>
                    <div class="position-relative">
                        <input type="password" id="password" name="password" class="form-control bg-light border-0 px-4" placeholder="Tạo mật khẩu..." style="border-radius: 12px; padding: 12px 15px;" required>
                        <span class="position-absolute" onclick="togglePassword('password', 'eye-icon-1')" style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d;">
                            <i class="fa-solid fa-eye-slash" id="eye-icon-1"></i>
                        </span>
                    </div>
                    @error('password') <span class="text-danger d-block mt-2" style="font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group mb-5">
                    <label class="font-weight-bold text-secondary mb-2" style="font-size: 0.85rem;">XÁC NHẬN MẬT KHẨU</label>
                    <div class="position-relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control bg-light border-0 px-4" placeholder="Nhập lại mật khẩu..." style="border-radius: 12px; padding: 12px 15px;" required>
                        <span class="position-absolute" onclick="togglePassword('password_confirmation', 'eye-icon-2')" style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d;">
                            <i class="fa-solid fa-eye-slash" id="eye-icon-2"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="btn w-100 text-white font-weight-bold mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #0284c7, #06b6d4); border: none; box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4); padding: 14px 0; font-size: 1.05rem;">
                    ĐĂNG KÝ TÀI KHOẢN
                </button>
                
                <div class="text-center mt-2">
                    <span class="text-muted" style="font-size: 0.95rem;">Đã có tài khoản?</span> 
                    <a href="{{ route('login') }}" class="font-weight-bold text-info text-decoration-none" style="font-size: 0.95rem;">Đăng nhập ngay</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon = document.getElementById(iconId);
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }
</script>
@endsection