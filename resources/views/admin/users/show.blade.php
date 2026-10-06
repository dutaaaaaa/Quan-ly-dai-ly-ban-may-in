@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4">Thông tin người dùng</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $user->id }}</p>
            <p><strong>Tên:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Vai trò:</strong> <span class="badge badge-{{ $user->role == 'admin' ? 'danger' : 'info' }}">{{ strtoupper($user->role) }}</span></p>
            <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-3"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
</div>
@endsection