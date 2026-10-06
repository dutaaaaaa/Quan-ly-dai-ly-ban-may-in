@extends('layouts.app')
@section('content')
<style>
    .chart-wrap { min-height: 360px; }
    .chart-wrap canvas { width: 100% !important; height: 360px !important; }
</style>
<div class="container-fluid">
    <h2 class="mb-4">Biểu đồ báo cáo doanh thu</h2>
    <nav class="nav nav-pills my-3 shadow-sm p-2 bg-white rounded">
        <a class="nav-link" href="{{ route('admin.reports.index') }}">Bảng số liệu</a>
        <a class="nav-link active font-weight-bold" aria-current="page" href="{{ route('admin.reports.charts') }}">Biểu đồ</a>
    </nav>
    <p class="text-muted">Chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy hoặc hoàn hàng.</p>
    
    <div id="report-chart-error" class="alert alert-warning d-none" role="alert">Không tải được thư viện biểu đồ.</div>
    
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white font-weight-bold">Doanh thu theo danh mục</div>
                <div class="card-body chart-wrap"><canvas id="categoryRevenueChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white font-weight-bold">Doanh thu theo ngày (30 ngày)</div>
                <div class="card-body chart-wrap"><canvas id="revenueByDateChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white font-weight-bold">Doanh thu theo tháng (12 tháng)</div>
                <div class="card-body chart-wrap"><canvas id="revenueByMonthChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white font-weight-bold">Doanh thu theo năm</div>
                <div class="card-body chart-wrap"><canvas id="revenueByYearChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold">Cơ cấu thanh toán (MoMo vs COD)</div>
                <div class="card-body chart-wrap"><canvas id="revenueByPaymentMethodChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<div id="report-chart-data" hidden data-chart-data="{{ json_encode([
    'catLabels' => $catLabels ?? [],
    'catRevenue' => $catRevenue ?? [],
    'revDateLabels' => $revDateLabels ?? [],
    'revDateData' => $revDateData ?? [],
    'revMonthLabels' => $revMonthLabels ?? [],
    'revMonthData' => $revMonthData ?? [],
    'revYearLabels' => $revYearLabels ?? [],
    'revYearData' => $revYearData ?? [],
    'paymentMethodLabels' => $paymentMethodLabels ?? [],
    'paymentMethodRevenue' => $paymentMethodRevenue ?? [],
]) }}"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', () => {
        if (typeof Chart === 'undefined') {
            document.getElementById('report-chart-error').classList.remove('d-none');
            return;
        }
        
        const reportData = JSON.parse(document.getElementById('report-chart-data').dataset.chartData);
        
        const mk = (el, type, labels, data, label) => new Chart(el, {
            type: type,
            data: { labels: labels, datasets: [{ label: label, data: data, fill: type === 'line', tension: 0.3, backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545', '#007bff', '#6610f2'] }] },
            options: { responsive: true, maintainAspectRatio: false, scales: type === 'pie' ? {} : { y: { beginAtZero: true } } }
        });

        mk(document.getElementById('categoryRevenueChart'), 'bar', reportData.catLabels, reportData.catRevenue, 'Doanh thu (VNĐ)');
        mk(document.getElementById('revenueByDateChart'), 'line', reportData.revDateLabels, reportData.revDateData, 'Doanh thu (VNĐ)');
        mk(document.getElementById('revenueByMonthChart'), 'bar', reportData.revMonthLabels, reportData.revMonthData, 'Doanh thu (VNĐ)');
        mk(document.getElementById('revenueByYearChart'), 'bar', reportData.revYearLabels, reportData.revYearData, 'Doanh thu (VNĐ)');
        
        new Chart(document.getElementById('revenueByPaymentMethodChart'), {
            type: 'pie',
            data: { 
                labels: reportData.paymentMethodLabels, 
                datasets: [{ label: 'Doanh thu (VNĐ)', data: reportData.paymentMethodRevenue, backgroundColor: ['#d63384', '#0dcaf0'] }] 
            }, 
            options: { responsive: true, maintainAspectRatio: false }
        });
    });
</script>
@endsection