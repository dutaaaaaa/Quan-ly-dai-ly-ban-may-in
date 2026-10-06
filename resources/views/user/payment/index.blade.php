@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <h2 class="mb-4 font-weight-bold text-info">Thanh Toán Đơn Hàng</h2>
    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm p-4 border-0 bg-white">
                <h4 class="mb-3 font-weight-bold">Thông tin & Phương thức</h4>
                @if ($errors->any())
    <div class="alert alert-danger shadow-sm">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                <form action="{{ route('user.payment.process') }}" method="POST">
    @csrf
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" name="name" class="form-control" value="{{ Auth::user()->name ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>

                    <!-- KHU VỰC CHỌN ĐỊA CHỈ GHN -->
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Tỉnh/Thành phố</label>
                            <select id="province_select" name="province_id" class="form-control" required>
                                <option value="">-- Đang tải... --</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Quận/Huyện</label>
                            <select id="district_select" name="to_district_id" class="form-control" required disabled>
                                <option value="">-- Chọn Quận/Huyện --</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Phường/Xã</label>
                            <select id="ward_select" name="to_ward_code" class="form-control" required disabled>
                                <option value="">-- Chọn Phường/Xã --</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ cụ thể (Số nhà, tên đường)</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Ví dụ: Số 10, Ngõ 15..." required></textarea>
                    </div>
                    
                    <!-- Input ẩn truyền tổng tiền đã cộng phí ship xuống Controller -->
                    <input type="hidden" name="total_price" id="total_price_input" value="{{ $totalPrice ?? 0 }}">

                   <h5 class="fw-bold mt-4 mb-3">Phương thức thanh toán</h5>

<div class="form-check mb-2">
    <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
    <label class="form-check-label" for="payment_cod">
        Thanh toán khi nhận hàng (COD)
    </label>
</div>

<div class="form-check mb-4">
    <input class="form-check-input" type="radio" name="payment_method" id="payment_momo" value="momo">
    <label class="form-check-label text-danger" for="payment_momo">
        Thanh toán qua Ví điện tử MoMo
    </label>
</div>

                    <button type="submit" class="btn btn-success btn-lg btn-block font-weight-bold">Xác nhận đặt hàng</button>
                </form>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm p-4 border-0 bg-white">
                <h4 class="mb-3 font-weight-bold">Đơn hàng của bạn</h4>
                <ul class="list-group mb-3">
                    @php $total = 0; @endphp
                    @foreach($cart as $item)
                        @php 
                            $subtotal = $item['price'] * $item['quantity']; 
                            $total += $subtotal; 
                        @endphp
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div><h6 class="my-0">{{ $item['name'] }}</h6><small class="text-muted">SL: {{ $item['quantity'] }}</small></div>
                            <span class="text-muted">{{ number_format($subtotal, 0, ',', '.') }} đ</span>
                        </li>
                    @endforeach

                    <!-- Hiển thị tiền hàng gốc -->
                    <li class="list-group-item d-flex justify-content-between bg-light">
                        <span class="text-muted">Tổng tiền hàng</span>
                        <span>{{ number_format($total, 0, ',', '.') }} đ</span>
                    </li>

                    <!-- Hiển thị phí ship GHN -->
                    <li class="list-group-item d-flex justify-content-between bg-light">
                        <span class="text-muted">Phí vận chuyển (GHN)</span>
                        <span class="text-danger font-weight-bold" id="shipping_fee_text">Chưa chọn địa chỉ</span>
                    </li>

                    <!-- Tổng thanh toán cuối cùng -->
                    <li class="list-group-item d-flex justify-content-between bg-white">
                        <span class="font-weight-bold">Tổng thanh toán</span>
                        <strong class="text-danger" id="final_total_text">{{ number_format($total, 0, ',', '.') }} đ</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT KẾT NỐI API GHN ĐÃ FIX LỖI CÚ PHÁP -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const provinceSelect = document.getElementById('province_select');
    const districtSelect = document.getElementById('district_select');
    const wardSelect = document.getElementById('ward_select');
    const shippingFeeText = document.getElementById('shipping_fee_text');
    const finalTotalText = document.getElementById('final_total_text');
    const totalPriceInput = document.getElementById('total_price_input');

    // Khai báo đường dẫn an toàn bằng chuỗi thay thế
    const provincesUrl = "{{ route('locations.provinces') }}";
    const districtsTemplate = "{{ route('locations.districts', ['provinceId' => 'REPLACE_ID']) }}";
    const wardsTemplate = "{{ route('locations.wards', ['districtId' => 'REPLACE_ID']) }}";
    const feeUrl = "{{ route('locations.fee') }}";

    const subtotal = parseInt("{{ $total ?? 0 }}") || 0;

    // 1. Tải danh sách Tỉnh/Thành phố
    fetch(provincesUrl)
        .then(res => res.json())
        .then(res => {
            if (res.data) {
                let options = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                res.data.forEach(p => {
                    options += `<option value="${p.ProvinceID}">${p.ProvinceName}</option>`;
                });
                provinceSelect.innerHTML = options;
            } else {
                provinceSelect.innerHTML = '<option value="">-- Không tải được tỉnh/thành --</option>';
            }
        })
        .catch(err => {
            console.error("Lỗi load tỉnh thành:", err);
            provinceSelect.innerHTML = '<option value="">-- Lỗi kết nối GHN --</option>';
        });

    // 2. Khi chọn Tỉnh -> Tải Quận/Huyện
    provinceSelect.addEventListener('change', function () {
        districtSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
        districtSelect.disabled = true;
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        wardSelect.disabled = true;
        updateTotals(0);

        if (!this.value) return;

        const url = districtsTemplate.replace('REPLACE_ID', this.value);

        fetch(url)
            .then(res => res.json())
            .then(res => {
                if (res.data) {
                    let options = '<option value="">-- Chọn Quận/Huyện --</option>';
                    res.data.forEach(d => {
                        options += `<option value="${d.DistrictID}">${d.DistrictName}</option>`;
                    });
                    districtSelect.innerHTML = options;
                    districtSelect.disabled = false;
                } else {
                    districtSelect.innerHTML = '<option value="">-- Không tải được quận/huyện --</option>';
                }
            })
            .catch(err => console.error("Lỗi load quận huyện:", err));
    });

    // 3. Khi chọn Quận/Huyện -> Tải Phường/Xã
    districtSelect.addEventListener('change', function () {
        wardSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
        wardSelect.disabled = true;
        updateTotals(0);

        if (!this.value) return;

        const url = wardsTemplate.replace('REPLACE_ID', this.value);

        fetch(url)
            .then(res => res.json())
            .then(res => {
                if (res.data) {
                    let options = '<option value="">-- Chọn Phường/Xã --</option>';
                    res.data.forEach(w => {
                        options += `<option value="${w.WardCode}">${w.WardName}</option>`;
                    });
                    wardSelect.innerHTML = options;
                    wardSelect.disabled = false;
                } else {
                    wardSelect.innerHTML = '<option value="">-- Không tải được phường/xã --</option>';
                }
            })
            .catch(err => console.error("Lỗi load phường xã:", err));
    });

    // 4. Khi chọn Phường/Xã -> Tính cước vận chuyển GHN
    wardSelect.addEventListener('change', function () {
        if (!this.value || !districtSelect.value) return;
        
        shippingFeeText.innerText = 'Đang tính cước...';

        fetch(feeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                to_district_id: districtSelect.value,
                to_ward_code: this.value
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.code === 200 && res.data) {
                const fee = parseInt(res.data.total) || 0;
                updateTotals(fee);
            } else {
                shippingFeeText.innerText = 'Chưa hỗ trợ tuyến này';
                updateTotals(0);
            }
        })
        .catch(err => {
            console.error("Lỗi tính phí:", err);
            shippingFeeText.innerText = 'Lỗi tính phí';
            updateTotals(0);
        });
    });

    // Hàm cập nhật tiền ship và tổng cộng
    function updateTotals(fee) {
        shippingFeeText.innerText = new Intl.NumberFormat('vi-VN').format(fee) + ' đ';
        const finalAmount = subtotal + fee;
        finalTotalText.innerText = new Intl.NumberFormat('vi-VN').format(finalAmount) + ' đ';
        if (totalPriceInput) {
            totalPriceInput.value = finalAmount;
        }
    }
});
</script>
@endsection