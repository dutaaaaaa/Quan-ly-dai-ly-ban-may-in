@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4">Chỉnh sửa người dùng</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="font-weight-bold">Tên</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Vai trò</label>
                    <select name="role" class="form-control" required>
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Người dùng</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Quản trị</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection