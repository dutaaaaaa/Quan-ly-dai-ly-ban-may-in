@extends('layouts.app')
@section('title', 'Giao dịch thanh toán')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header và Navigation -->
    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
        <h3 class="fw-semibold text-dark m-0">Giao dịch thanh toán</h3>
        <span class="text-muted small">Tra cứu thanh toán theo đơn hàng và cập nhật trạng thái COD.</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabs điều hướng -->
    <ul class="nav nav-tabs mb-4 border-0">
        <li class="nav-item">
            <a class="nav-link text-primary fw-medium" href="{{ route('admin.finance.index') }}">Thống kê chỉ số</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-bold bg-dark text-white rounded-top" href="{{ route('admin.finance.transactions') }}">Giao dịch thanh toán</a>
        </li>
    </ul>

    <!-- Form Lọc Nâng Cao -->
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body">
            <form action="{{ route('admin.finance.transactions') }}" method="GET" class="row g-3">
                
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
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Số tiền từ (đ)</label>
                    <input type="number" name="min_amount" class="form-control form-control-sm" placeholder="Không giới hạn" value="{{ request('min_amount') }}">
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold">Sắp xếp</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                        <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Giá trị tăng dần</option>
                        <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Giá trị giảm dần</option>
                    </select>
                </div>

                <!-- Hàng Buttons -->
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-medium">Áp dụng bộ lọc</button>
                    <a href="{{ route('admin.finance.transactions') }}" class="btn btn-outline-secondary btn-sm px-4 ms-2 fw-medium">Xóa bộ lọc</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Thông báo kết quả lọc -->
    <p class="text-muted small mb-3">
        Có <strong>{{ $orders->total() }}</strong> giao dịch phù hợp. Số tiền bao gồm phí vận chuyển, ngày lọc là ngày tạo đơn.
    </p>

    <!-- Bảng Danh Sách Giao Dịch -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="fw-medium py-3 ps-4">Đơn hàng</th>
                            <th class="fw-medium">Khách hàng</th>
                            <th class="fw-medium text-center">Phương thức</th>
                            <th class="fw-medium text-end">Số tiền</th>
                            <th class="fw-medium text-center">Thanh toán</th>
                            <th class="fw-medium pe-4">Cập nhật COD</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($orders as $order)
                            @php
                                $statusColor = match($order->payment_status) {
                                    'paid', 'refunded' => 'success',
                                    'pending', 'initiated', 'refund_pending' => 'warning',
                                    'failed', 'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <tr>
                                <!-- Thông tin Đơn hàng -->
                                <td class="ps-4">
                                    <span class="fw-bold text-primary">#{{ $order->id }}</span><br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</small>
                                </td>
                                
                                <!-- Thông tin Khách hàng -->
                                <td>
                                    <span class="fw-medium text-dark">{{ $order->name ?? 'Khách lẻ' }}</span><br>
                                    <small class="text-muted">{{ $order->phone ?? 'N/A' }}</small>
                                </td>

                                <!-- Phương thức thanh toán -->
                                <td class="text-center">
                                    <span class="badge border border-secondary text-secondary">
                                        {{ $methods[$order->gateway] ?? 'Chưa xác định' }}
                                    </span>
                                </td>

                                <!-- Số tiền -->
                                <td class="text-end fw-semibold text-danger">
                                    {{ number_format($order->total_price, 0, ',', '.') }} đ
                                </td>

                                <!-- Trạng thái Thanh toán -->
                                <td class="text-center">
                                    <span class="badge bg-{{ $statusColor }} bg-opacity-75 px-2 py-1">
                                        {{ $statuses[$order->payment_status] ?? $order->payment_status }}
                                    </span>
                                </td>

                                <!-- Cập nhật trạng thái COD -->
                                <td class="pe-4">
                                    @if($order->gateway === 'cod')
                                        @php $transitions = $codTransitions[$order->payment_status] ?? []; @endphp
                                        @if(count($transitions) > 1)
                                            <form action="{{ route('admin.finance.update-status', $order->id) }}" method="POST" class="d-flex align-items-center m-0">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="current_payment_status" value="{{ $order->payment_status }}">
                                                <input type="hidden" name="current_order_status" value="{{ $order->status }}">
                                                <input type="hidden" name="current_payment_id" value="{{ $order->payment_id ?? 0 }}">
                                                
                                                <select name="payment_status" class="form-select form-select-sm me-2 w-auto shadow-none">
                                                    @foreach($transitions as $t)
                                                        <option value="{{ $t }}" {{ $order->payment_status === $t ? 'selected' : '' }}>
                                                            {{ $statuses[$t] ?? $t }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-info text-white fw-medium shadow-none">Lưu</button>
                                            </form>
                                        @else
                                            <span class="text-muted small"><i class="fa-solid fa-lock text-secondary"></i> Không thể đổi</span>
                                        @endif
                                    @else
                                        <span class="text-muted small"><i class="fa-solid fa-ban text-secondary"></i> Không áp dụng</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fa-solid fa-box-open fs-3 mb-2 d-block"></i>
                                    Không có đơn hàng phù hợp với bộ lọc.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Phân trang -->
            @if($orders->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Tinh chỉnh style -->
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
    .fw-medium { font-weight: 500 !important; }
    .fw-semibold { font-weight: 600 !important; }
    .nav-tabs .nav-link { border: none; border-bottom: 2px solid transparent; color: #495057; }
    .nav-tabs .nav-link.active { border-bottom-color: transparent; }
    .form-control-sm, .form-select-sm { border-radius: 4px; border: 1px solid #ced4da; padding: 0.4rem 0.5rem; }
    .badge { font-weight: 500; font-size: 0.8rem; }
</style>
@endsection