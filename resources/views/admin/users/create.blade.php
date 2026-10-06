@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4">Thêm người dùng mới</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="font-weight-bold">Tên</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Vai trò</label>
                    <select name="role" class="form-control" required>
                        <option value="user">Người dùng (User)</option>
                        <option value="admin">Quản trị (Admin)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Lưu tài khoản</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection