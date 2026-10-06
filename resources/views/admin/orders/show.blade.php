@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết Đơn hàng #{{ $order->order_code ?? $order->id }}</h2>
        <a href="{{ url('admin/orders') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <!-- Thông tin Khách hàng & Giao hàng -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white font-weight-bold">
                    Thông tin Khách hàng
                </div>
                <div class="card-body">
                    <p><strong>Họ tên:</strong> {{ $order->name ?? ($order->user->name ?? 'Khách lẻ') }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $order->phone ?? 'N/A' }}</p>
                    <p><strong>Địa chỉ giao hàng:</strong> {{ $order->address ?? 'N/A' }}</p>
                    <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Thông tin Thanh toán & Trạng thái -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white font-weight-bold">
                    Thông tin Thanh toán
                </div>
                <div class="card-body">
                    <p>
                        <strong>Trạng thái đơn:</strong>
                        @if($order->status == 'pending') 
                            <span class="badge bg-warning text-dark">Chờ xử lý</span>
                        @elseif(in_array($order->status, ['paid', 'cod_paid', 'paid_momo'])) 
                            <span class="badge bg-success">Đã thanh toán</span>
                        @elseif($order->status == 'cancelled') 
                            <span class="badge bg-danger">Đã hủy</span>
                        @else 
                            <span class="badge bg-secondary">{{ strtoupper($order->status) }}</span>
                        @endif
                    </p>
                    <p class="mb-2">
                        <strong>Vận chuyển:</strong>
                        @if($order->shipping_status && $order->shipping_status !== 'pending')
                            <span class="text-info fw-bold">{{ $order->shipping_status }}</span>
                        @else
                            <span class="text-muted">Chưa cập nhật</span>
                        @endif
                    </p>
                    <!-- Đã thêm phần hiển thị Mã vận đơn GHN vào đây -->
                    <p class="mb-2">
                        <strong>Mã vận đơn GHN:</strong>
                        @if($order->ghn_order_code)
                            <span class="badge bg-primary px-2 py-1">{{ $order->ghn_order_code }}</span>
                        @else
                            <span class="text-muted fst-italic">Chưa có (Hoặc chưa tạo)</span>
                        @endif
                    </p>
                    <p><strong>Phương thức:</strong> {{ strtoupper($order->payment_method ?? 'COD') }}</p>
                    <p><strong>Phí giao hàng (GHN):</strong> <span class="text-danger">{{ number_format($order->ghn_total_fee ?? 0, 0, ',', '.') }} đ</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách Sản phẩm -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white font-weight-bold">
            Sản phẩm đã đặt
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-right">Đơn giá</th>
                            <th class="text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->orderItems as $item)
                        <tr>
                            <td>
                                @if($item->product && $item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <div style="width: 50px; height: 50px; background: #e9ecef; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-box text-secondary"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $item->product->name ?? 'Sản phẩm không xác định' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                            <td class="text-right font-weight-bold text-danger">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Không tìm thấy chi tiết sản phẩm.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="4" class="text-right h5 mb-0">Tổng tiền thanh toán:</td>
                            <td class="text-right text-danger h5 mb-0">{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection