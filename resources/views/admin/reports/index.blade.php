@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Báo cáo doanh thu</h2>
    <nav class="nav nav-pills my-3 shadow-sm p-2 bg-white rounded">
        <a class="nav-link active font-weight-bold" aria-current="page" href="{{ route('admin.reports.index') }}">Bảng số liệu</a>
        <a class="nav-link" href="{{ route('admin.reports.charts') }}">Biểu đồ</a>
    </nav>
    <p class="text-muted">Doanh thu tính theo ngày tạo đơn, chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy hoặc hoàn hàng.</p>
    
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0 border-left-primary">
                <span class="text-muted font-weight-bold">Tổng số đơn hàng</span>
                <h3 class="mb-0 text-primary mt-2">{{ number_format($totalOrders) }}</h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0 border-left-info">
                <span class="text-muted font-weight-bold">Tổng số khách hàng</span>
                <h3 class="mb-0 text-info mt-2">{{ number_format($totalCustomers) }}</h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0 border-left-success">
                <span class="text-muted font-weight-bold">Doanh thu (gồm phí vận chuyển)</span>
                <h3 class="mb-0 text-success mt-2">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h3>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white">
            <strong class="text-dark">Doanh thu theo danh mục</strong>
            <div class="small text-muted mt-1">Tính theo giá sản phẩm khi đặt hàng, không gồm phí vận chuyển.</div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">Danh mục</th>
                        <th class="text-right">Số lượng bán</th>
                        <th class="text-right px-4">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryRevenue as $revenue)
                    <tr>
                        <td class="px-4 font-weight-bold">{{ $revenue->category_name ?? ('Danh mục #'.$revenue->category_id) }}</td>
                        <td class="text-right">{{ number_format($revenue->total_qty) }}</td>
                        <td class="text-right px-4 font-weight-bold text-danger">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Chưa có doanh thu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach([
        ['Doanh thu theo ngày', 'Ngày', 'date', $revenueByDate, 'd/m/Y'],
        ['Doanh thu theo tháng', 'Tháng', 'month', $revenueByMonth, 'm/Y'],
        ['Doanh thu theo năm', 'Năm', 'year', $revenueByYear, null],
    ] as [$title, $label, $field, $rows, $format])
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white font-weight-bold text-dark">{{ $title }}</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">{{ $label }}</th>
                        <th class="text-right">Số đơn đã thanh toán</th>
                        <th class="text-right px-4">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $revenue)
                    <tr>
                        <td class="px-4">{{ $format ? \Carbon\Carbon::parse($revenue->{$field} . ($field === 'month' ? '-01' : ''))->format($format) : $revenue->{$field} }}</td>
                        <td class="text-right">{{ number_format($revenue->order_count) }}</td>
                        <td class="text-right px-4 font-weight-bold text-danger">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Chưa có doanh thu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection