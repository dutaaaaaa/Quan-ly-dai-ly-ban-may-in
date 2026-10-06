@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white p-3 shadow-sm border">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row bg-white p-4 rounded shadow-sm border">
        <!-- Cột hiển thị ảnh sản phẩm -->
        <div class="col-md-5 text-center">
            @php
                $imgUrl = !empty($product->image) 
                    ? asset('storage/' . $product->image) 
                    : 'https://dummyimage.com/400x400/dee2e6/6c757d.png&text=No+Image';
            @endphp
            <img src="{{ $imgUrl }}" class="img-fluid rounded border p-2 bg-light" alt="{{ $product->name }}" style="max-height: 350px; object-fit: contain;">
        </div>

        <!-- Cột thông tin chi tiết -->
        <div class="col-md-7">
            <h2 class="font-weight-bold text-dark mb-3">{{ $product->name }}</h2>
            <p class="text-secondary mb-2">Hãng sản xuất: <strong class="text-info">{{ $product->brand }}</strong></p>
            <p class="text-secondary mb-3">Loại máy: <strong class="text-info">{{ $product->category->name ?? 'Chưa phân loại' }}</strong></p>
            
            <h3 class="text-danger font-weight-bold mb-4">
    @if($product->variants->count() > 0)
        {{ number_format($product->variants->min('price'), 0, ',', '.') }} đ
    @else
        Liên hệ
    @endif
</h3>

            <!-- Danh sách các phiên bản màu sắc & giá -->
            <h5 class="font-weight-bold text-dark mb-3">Phiên bản có sẵn:</h5>
            <div class="list-group mb-4">
                @foreach($product->variants as $variant)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Màu: {{ $variant->color }}</strong> 
                            <span class="text-muted ml-2">(SKU: {{ $variant->sku }})</span>
                        </div>
                        <div>
                            <span class="text-danger font-weight-bold mr-3">{{ number_format($variant->price, 0, ',', '.') }} ₫</span>
                            <span class="badge {{ $variant->stock > 0 ? 'badge-success' : 'badge-danger' }}">
                                {{ $variant->stock > 0 ? 'Còn hàng (' . $variant->stock . ')' : 'Hết hàng' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Nút mua hàng / thêm giỏ hàng -->
            <form action="#" method="POST">
                @csrf
                <button type="submit" class="btn btn-success btn-lg font-weight-bold px-5" style="border-radius: 8px;">
                    <i class="fa-solid fa-cart-shopping mr-2"></i> Thêm vào giỏ hàng
                </button>
            </form>
        </div>
    </div>
</div>
@endsection