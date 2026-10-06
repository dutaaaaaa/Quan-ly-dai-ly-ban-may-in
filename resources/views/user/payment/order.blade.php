@extends('layouts.app')

@section('content')
<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #0aa2a0; font-weight: bold;">Lịch Sử Đơn Hàng</h2>
        <a href="{{ route('user.payment.index') }}" class="btn btn-outline-secondary">Tiếp tục mua sắm</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning shadow-sm">{{ session('warning') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã ĐH</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Vận chuyển (GHN)</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">#{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="fw-bold text-danger">{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                            <td>
                                @if($order->status == 'paid')
                                    <span class="badge bg-success rounded-pill px-3 py-2">Đã thanh toán</span>
                                @elseif($order->status == 'cod_ordered')
                                    <span class="badge bg-info text-dark rounded-pill px-3 py-2">Thanh toán COD</span>
                                @else
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Chờ thanh toán</span>
                                @endif
                            </td>
                            <td>
                                @if($order->shipping_status == 'ready_to_pick')
                                    <span class="badge bg-primary rounded-pill px-3 py-2">Chờ lấy hàng</span>
                                    <div class="small text-muted mt-1">Mã: {{ $order->ghn_order_code }}</div>
                                @elseif($order->shipping_status == 'processing')
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Đang xử lý</span>
                                @else
                                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2">Khởi tạo</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('user.orders.show', $order->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">Xem chi tiết</a>
                                
                                {{-- Nút thanh toán lại MoMo nếu đơn bị lỗi/chưa thanh toán --}}
                                @if($order->status == 'pending')
                                    <a href="{{ route('user.orders.momo.pay', $order->id) }}" class="btn btn-sm btn-success rounded-pill px-3 mt-1 mt-md-0">Thanh toán MoMo</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 text-light"></i>
                                <p>Bạn chưa có đơn hàng nào.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{ $orders->links() }}
    </div>
</div>
@endsection