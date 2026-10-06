@extends('layouts.app')
@section('content')

<!-- Toast Thông báo góc màn hình (Đã fix lỗi tự động hiện) -->
<div class="position-fixed top-0 right-0 p-3" style="z-index: 1050; right: 0; top: 70px;">
  <!-- Thêm display: none và opacity: 0 trực tiếp vào style để ép ẩn hoàn toàn lúc mới load -->
  <div id="cartToast" class="bg-success text-white font-weight-bold shadow p-3 rounded" style="display: none; opacity: 0; transition: opacity 0.3s ease-in-out;" role="alert">
    <div class="toast-body" style="font-size: 1.1rem; margin: 0;">
      <i class="fa-solid fa-circle-check mr-2"></i> <span id="toastMessage">Sản phẩm đã được thêm!</span>
    </div>
  </div>
</div>

<!-- Khu vực hiển thị Sản phẩm -->
<div class="container mt-4">
    <h4 class="mb-4 font-weight-bold border-bottom pb-2 text-uppercase">Sản phẩm nổi bật</h4>
    
    <div class="row">
        @forelse($products as $product)
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 10px; overflow: hidden; background-color: #f8f9fa;">
                    
                    @php
                        $imgUrl = !empty($product->image) 
                            ? asset('storage/' . $product->image) 
                            : 'https://dummyimage.com/400x400/dee2e6/6c757d.png&text=No+Image';
                    @endphp

                    <img src="{{ $imgUrl }}" class="card-img-top bg-white" alt="{{ $product->name }}" style="height: 200px; object-fit: contain; border-bottom: 1px solid #eee; padding: 10px;">
                    
                    <div class="card-body text-center d-flex flex-column bg-white">
                        <h6 class="card-title font-weight-bold text-dark mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $product->name }}
                        </h6>
                        
                        <p class="card-text text-danger font-weight-bold mt-auto mb-3" style="font-size: 1.1rem;">
                            @if($product->variants->count() > 0)
                                {{ number_format($product->variants->min('price'), 0, ',', '.') }} đ
                            @else
                                Liên hệ
                            @endif
                        </p>
                        
                        <!-- CỤM NÚT BẤM THAO TÁC -->
                        <div class="mt-auto w-100">
                            <!-- Nút Xem chi tiết -->
                            <a href="{{ route('product.detail', $product->id) }}" class="btn btn-outline-info btn-sm w-100 font-weight-bold mb-2" style="border-radius: 8px;">
                                <i class="fa-solid fa-eye mr-1"></i> Xem chi tiết
                            </a>
                            
                            <!-- Nút Thêm vào giỏ hàng -->
                           <form action="{{ route('cart.add', $product->id) }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-success btn-block">
        <i class="fa fa-shopping-cart"></i> Thêm giỏ hàng
    </button>
</form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">
                <p style="font-size: 1.2rem;">Hiện tại cửa hàng đang cập nhật sản phẩm. Vui lòng quay lại sau!</p>
            </div>
        @endforelse
    </div>
</div>

<!-- ĐOẠN SCRIPT XỬ LÝ FETCH API (JAVASCRIPT THUẦN) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const addButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            const csrfToken = '{{ csrf_token() }}';

          fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ _token: csrfToken })
            })
            .then(response => {
                // ĐÃ THÊM: Bắt lỗi 401 nếu chưa đăng nhập
                if (response.status === 401) {
                    alert("Bạn cần đăng nhập tài khoản để thêm sản phẩm vào giỏ hàng!");
                    window.location.href = "{{ route('login') }}"; 
                    return Promise.reject("Chưa đăng nhập");
                }
                if (!response.ok) throw new Error("Lỗi phản hồi từ server");
                return response.json();
            })
            .then(data => {
                // ... (Phần hiển thị Toast thành công giữ nguyên như cũ)
                if(data.success) {
                    document.getElementById('toastMessage').innerText = data.message;
                    const toastEl = document.getElementById('cartToast');
                    toastEl.style.display = "block"; 
                    setTimeout(() => { toastEl.style.opacity = "1"; }, 50);
                    setTimeout(function() {
                        toastEl.style.opacity = "0"; 
                        setTimeout(() => { toastEl.style.display = "none"; }, 300); 
                    }, 3000);
                } else {
                    alert(data.message);
                }
            })
</script>
@endsection