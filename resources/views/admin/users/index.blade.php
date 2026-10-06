@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản Lý Tài Khoản Khách Hàng</h2>
    <!-- Bổ sung nút Thêm theo Lab 08 -->
    <a href="{{ route('admin.users.create') }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Thêm người dùng</a>
</div>

<div class="card border-0 shadow-sm rounded">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 px-4 py-3">ID</th>
                    <th class="border-0 py-3">Họ và Tên</th>
                    <th class="border-0 py-3">Email</th>
                    <th class="border-0 py-3">Phân Quyền</th>
                    <th class="border-0 py-3">Ngày Đăng Ký</th>
                    <th class="border-0 py-3 text-center" width="200px">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td class="px-4 py-3">{{ $user->id }}</td>
                    <td class="py-3 font-weight-bold text-primary">{{ $user->name }}</td>
                    <td class="py-3">{{ $user->email }}</td>
                    <td class="py-3">
                        <!-- Hỗ trợ cả 2 kiểu dữ liệu (số 1 hoặc chữ 'admin') -->
                        @if($user->role == 1 || $user->role === 'admin')
                            <span class="badge badge-danger px-2 py-1">Admin</span>
                        @else
                            <span class="badge badge-secondary px-2 py-1">Khách hàng</span>
                        @endif
                    </td>
                    <td class="py-3">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-3 text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <!-- Nút Xem & Sửa theo chuẩn Lab 08 -->
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-info mr-1">
                                <i class="fa-solid fa-eye"></i> Xem
                            </a>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary mr-1">
                                <i class="fa-solid fa-pen-to-square"></i> Sửa
                            </a>
                            
                            <!-- Nút Xóa (Đã cập nhật route chuẩn) -->
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection