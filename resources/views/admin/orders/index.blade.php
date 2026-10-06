@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Quản lý Đơn hàng</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Khu vực Lọc trạng thái -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-light rounded">
            <form action="{{ url('admin/orders') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="statusFilter" class="form-label font-weight-bold text-dark">Lọc theo trạng thái đơn hàng</label>
                    <select name="status" id="statusFilter" class="form-select border-primary">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý (Chưa xác nhận)</option>
                        <!-- Đã sửa value thành 'paid' để khớp với dữ liệu thanh toán MoMo trong Database -->
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thanh toán (MoMo)</option>
                        <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Đã hoàn thành</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter"></i> Lọc dữ liệu
                    </button>
                </div>
                @if(request('status'))
                <div class="col-md-2">
                    <a href="{{ url('admin/orders') }}" class="btn btn-outline-secondary w-100">
                        <i class="fa-solid fa-rotate-left"></i> Bỏ lọc
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <!-- Đã sửa: Ưu tiên hiển thị mã code phía khách hàng, nếu không có mới dùng ID -->
                            <td class="font-weight-bold">#{{ $order->order_code ?? $order->id }}</td>
                            <td>{{ $order->name ?? ($order->user->name ?? 'Khách lẻ') }}</td>
                            <td>{{ $order->phone ?? 'N/A' }}</td>
                            <td class="text-danger font-weight-bold">{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                            <td>
                                @if($order->status == 'pending') 
                                    <span class="badge bg-warning text-dark px-2 py-1">Chờ xử lý</span>
                                @elseif(in_array($order->status, ['paid', 'cod_paid', 'paid_momo'])) 
                                    <span class="badge bg-success px-2 py-1">Đã thanh toán</span>
                                @elseif($order->status == 'cancelled') 
                                    <span class="badge bg-danger px-2 py-1">Đã hủy</span>
                                @else 
                                    <span class="badge bg-secondary px-2 py-1">{{ strtoupper($order->status) }}</span>
                                @endif
                                
                                <!-- Hiển thị thêm Trạng thái Vận chuyển nếu có -->
                                @if($order->shipping_status && $order->shipping_status !== 'pending')
                                    <br><small class="text-info fw-bold"><i class="fa-solid fa-truck"></i> {{ $order->shipping_status }}</small>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <!-- Đã thêm: NÚT XEM CHI TIẾT -->
                                <a href="{{ url('admin/orders/' . $order->id) }}" class="btn btn-sm btn-primary mb-1" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i> Xem
                                </a>

                                <!-- NÚT XÓA -->
                                <form action="{{ url('admin/orders/' . $order->id) }}" method="POST" class="d-inline mb-1" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa đơn hàng">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Chưa có đơn hàng nào trong hệ thống.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Phân trang -->
            <div class="d-flex justify-content-end mt-3">
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection