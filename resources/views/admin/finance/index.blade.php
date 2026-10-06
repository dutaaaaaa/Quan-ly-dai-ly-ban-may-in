@extends('layouts.app')
@section('title', 'Thống kê tài chính')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header và Navigation Navigation -->
    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
        <h3 class="fw-semibold text-dark m-0">Thống kê tài chính</h3>
        <span class="text-muted small">Tổng hợp giá trị thanh toán theo trạng thái và phương thức.</span>
    </div>

    <!-- Tabs điều hướng -->
    <ul class="nav nav-tabs mb-4 border-0">
        <li class="nav-item">
            <a class="nav-link active fw-bold bg-dark text-white rounded-top" href="{{ route('admin.finance.index') }}">Thống kê chỉ số</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-primary fw-medium" href="{{ route('admin.finance.transactions') }}">Giao dịch thanh toán</a>
        </li>
    </ul>

    <!-- Form Lọc Nâng Cao -->
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body">
            <form action="{{ route('admin.finance.index') }}" method="GET" class="row g-3">
                
                <!-- Hàng 1 -->
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Tìm đơn hàng</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Mã đơn, tên hoặc số điện thoại" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Từ ngày tạo đơn</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Đến ngày tạo đơn</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>

                <!-- Hàng 2 -->
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Số tiền từ (đ)</label>
                    <input type="number" name="min_amount" class="form-control form-control-sm" placeholder="Không giới hạn" value="{{ request('min_amount') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Số tiền đến (đ)</label>
                    <input type="number" name="max_amount" class="form-control form-control-sm" placeholder="Không giới hạn" value="{{ request('max_amount') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Phương thức</label>
                    <select name="gateway" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        @foreach($methods as $key => $label)
                            <option value="{{ $key }}" {{ request('gateway') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Trạng thái thanh toán</label>
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('payment_status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Hàng Buttons -->
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-medium">Áp dụng bộ lọc</button>
                    <a href="{{ route('admin.finance.index') }}" class="btn btn-outline-secondary btn-sm px-4 ms-2 fw-medium">Xóa bộ lọc</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Thông báo kết quả lọc -->
    <p class="text-muted small mb-3">
        Có <strong>{{ $summary->order_count ?? 0 }}</strong> đơn phù hợp. Số tiền bao gồm phí vận chuyển, thống kê theo ngày tạo đơn trên toàn bộ kết quả lọc.
    </p>

    <!-- Các Khối Chỉ Số Tổng Hợp -->
    <div class="row g-3 mb-4">
        <!-- Tổng giá trị -->
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm border-start border-4 border-primary">
                <div class="card-body">
                    <h6 class="text-muted fw-normal mb-2">Tổng giá trị đơn hàng</h6>
                    <h4 class="text-dark fw-semibold mb-1">{{ number_format($summary->total_amount ?? 0, 0, ',', '.') }} đ</h4>
                    <span class="small text-secondary">{{ $summary->order_count ?? 0 }} đơn, bao gồm đơn đã hủy</span>
                </div>
            </div>
        </div>

        @foreach($statuses as $statusKey => $statusLabel)
            @php
                $statusData = $statusTotals->get($statusKey);
                $count = $statusData ? $statusData->order_count : 0;
                $amount = $statusData ? $statusData->total_amount : 0;
                
                // Gán màu cho từng trạng thái cho dễ nhìn
                $borderColor = match($statusKey) {
                    'paid', 'refunded' => 'border-success',
                    'pending', 'initiated', 'refund_pending' => 'border-warning',
                    'failed', 'cancelled' => 'border-danger',
                    default => 'border-secondary'
                };
            @endphp
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm border-start border-4 {{ $borderColor }}">
                    <div class="card-body">
                        <h6 class="text-muted fw-normal mb-2">{{ $statusLabel }}</h6>
                        <h4 class="text-{{ str_replace('border-', '', $borderColor) }} fw-semibold mb-1">
                            {{ number_format($amount, 0, ',', '.') }} đ
                        </h4>
                        <span class="small text-secondary">{{ $count }} đơn</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Bảng Thống Kê Theo Phương Thức -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light border-bottom-0 pt-3 pb-2">
            <h6 class="fw-bold text-dark m-0">Thống kê theo phương thức</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="fw-medium py-3 ps-4">Phương thức</th>
                            <th class="fw-medium text-center">Số đơn</th>
                            <th class="fw-medium text-end">Tổng giá trị</th>
                            <th class="fw-medium text-end pe-4">Đã thanh toán</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($methods as $methodKey => $methodLabel)
                            @php
                                $methodData = $methodTotals->get($methodKey);
                                $orderCount = $methodData ? $methodData->order_count : 0;
                                $totalAmt = $methodData ? $methodData->total_amount : 0;
                                $paidAmt = $methodData ? $methodData->paid_amount : 0;
                            @endphp
                            <tr>
                                <td class="ps-4 fw-medium text-dark">{{ $methodLabel }}</td>
                                <td class="text-center text-secondary">{{ $orderCount }}</td>
                                <td class="text-end text-secondary">{{ number_format($totalAmt, 0, ',', '.') }} đ</td>
                                <td class="text-end fw-semibold text-success pe-4">{{ number_format($paidAmt, 0, ',', '.') }} đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- CSS Bổ sung để tinh chỉnh font chữ và khoảng cách -->
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
    .fw-medium { font-weight: 500 !important; }
    .fw-semibold { font-weight: 600 !important; }
    .text-muted { color: #6c757d !important; }
    .nav-tabs .nav-link { border: none; border-bottom: 2px solid transparent; color: #495057; }
    .nav-tabs .nav-link.active { border-bottom-color: transparent; }
    .card { border-radius: 8px; }
    .form-control-sm, .form-select-sm { border-radius: 4px; border: 1px solid #ced4da; padding: 0.4rem 0.5rem; }
    .form-label { margin-bottom: 0.25rem; }
</style>
@endsection