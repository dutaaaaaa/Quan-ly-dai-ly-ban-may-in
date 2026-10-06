@extends('layouts.app')

@section('content')
<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #0aa2a0; font-weight: bold;">Chi Tiết Đơn Hàng #{{ $order->id }}</h2>
        <a href="{{ route('user.orders.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>

    <div class="row">
        <!-- Cột thông tin khách hàng và giao hàng -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="fas fa-map-marker-alt text-danger me-2"></i> Thông tin nhận hàng</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Họ và tên:</strong> {{ $order->name }}</p>
                    <p class="mb-2"><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
                    <p class="mb-2"><strong>Địa chỉ:</strong> {{ $order->address }}</p>
                    <hr>
                    <h6 class="fw-bold mt-3"><i class="fas fa-truck text-info me-2"></i> Trạng thái vận chuyển</h6>
                    <p class="mb-1">Tình trạng: 
                        @if($order->shipping_status == 'ready_to_pick') <strong class="text-primary">Đang chờ lấy hàng</strong>
                        @elseif($order->shipping_status == 'processing') <strong class="text-secondary">Đang xử lý</strong>
                        @else <strong>Khởi tạo</strong> @endif
                    </p>
                    @if($order->ghn_order_code)
                        <p class="mb-0">Mã vận đơn (GHN): <strong class="text-dark">{{ $order->ghn_order_code }}</strong></p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cột danh sách sản phẩm -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="fas fa-shopping-bag text-success me-2"></i> Sản phẩm đã đặt</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-borderless align-middle mb-0 mt-3">
                        <thead class="border-bottom text-muted">
                            <tr>
                                <th class="ps-4">Sản phẩm</th>
                                <th class="text-center">Đơn giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end pe-4">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr class="border-bottom">
                                <td class="ps-4 py-3">
                                    <span class="fw-semibold">{{ $item->product->name ?? 'Sản phẩm không xác định' }}</span>
                                </td>
                                <td class="text-center">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                <td class="text-center">x{{ $item->quantity }}</td>
                                <td class="text-end pe-4 fw-bold">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top-0 pt-3 pb-4 pe-4 text-end">
                    <div class="d-flex justify-content-end mb-2">
                        <span class="text-muted me-4">Phí vận chuyển:</span>
                        <span class="fw-semibold">{{ number_format($order->ghn_total_fee ?? 0, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="d-flex justify-content-end align-items-center mt-2">
                        <span class="text-muted me-4">Tổng thanh toán:</span>
                        <span class="fs-4 fw-bold text-danger">{{ number_format($order->total_price, 0, ',', '.') }} đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection