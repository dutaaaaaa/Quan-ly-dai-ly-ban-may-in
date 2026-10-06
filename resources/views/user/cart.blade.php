@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h2 class="mb-4 font-weight-bold text-info">Giỏ Hàng Của Bạn</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php $total = 0; @endphp

    @if(session('cart') && count(session('cart')) > 0)
        <!-- Bọc form để chuẩn bị submit các sản phẩm được tích checkbox -->
        <form action="{{ route('checkout.index') }}" method="GET">
            <table class="table table-bordered bg-white shadow-sm">
                <thead class="bg-light">
                    <tr>
                        <!-- YÊU CẦU: NÚT CHECKBOX -->
                        <th class="text-center align-middle" style="width: 50px;">
                            <input type="checkbox" id="selectAll" title="Chọn tất cả">
                        </th>
                        <th>Hình Ảnh</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Đơn Giá</th>
                        <th>Số Lượng</th>
                        <th>Thành Tiền</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('cart') as $id => $details)
                        @php 
                            $subtotal = $details['price'] * $details['quantity'];
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <!-- YÊU CẦU: CHECKBOX TỪNG SẢN PHẨM -->
                            <td class="text-center align-middle">
                                <input type="checkbox" name="selected_items[]" value="{{ $id }}" class="item-checkbox" checked>
                            </td>
                            <td class="text-center" style="width: 100px;">
                                <img src="{{ !empty($details['image']) ? asset('storage/' . $details['image']) : 'https://dummyimage.com/100x100/dee2e6/6c757d.png&text=No+Image' }}" width="60px" class="rounded">
                            </td>
                            <td class="align-middle font-weight-bold">{{ $details['name'] }}</td>
                            <td class="align-middle text-danger font-weight-bold">{{ number_format($details['price'], 0, ',', '.') }} đ</td>
                            <td class="align-middle text-center" style="width: 100px;">{{ $details['quantity'] }}</td>
                            <td class="align-middle text-danger font-weight-bold">{{ number_format($subtotal, 0, ',', '.') }} đ</td>
                            <td class="align-middle text-center" style="width: 80px;">
                                <!-- Dùng button type=button gọi hàm JS để không bị submit nhầm sang checkout -->
                                <button type="button" class="btn btn-sm btn-danger" onclick="document.getElementById('delete-form-{{ $id }}').submit();">Xóa</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded shadow-sm border">
                <h4 class="m-0">Tổng tiền dự kiến: <span class="text-danger font-weight-bold">{{ number_format($total, 0, ',', '.') }} đ</span></h4>
                <div>
                    <a href="{{ route('welcome') }}" class="btn btn-secondary mr-2">Tiếp tục mua sắm</a>
                    <button type="submit" class="btn btn-success px-4 py-2 font-weight-bold">Tiến hành thanh toán</button>
                </div>
            </div>
        </form>

        <!-- Form xóa sản phẩm phải tách riêng ra ngoài -->
        @foreach(session('cart') as $id => $details)
            <form id="delete-form-{{ $id }}" action="{{ route('cart.remove', $id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

    @else
        <div class="text-center bg-white p-5 rounded shadow-sm border">
            <p class="text-muted" style="font-size: 1.2rem;">Giỏ hàng của bạn đang trống!</p>
            <a href="{{ route('welcome') }}" class="btn btn-info font-weight-bold">Quay lại mua sắm ngay</a>
        </div>
    @endif
</div>

<!-- Script để chọn tất cả checkbox -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    
    if(selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }
});
</script>
@endsection