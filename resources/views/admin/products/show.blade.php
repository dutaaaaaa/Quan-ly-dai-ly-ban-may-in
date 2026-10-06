@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb mb-4 d-flex justify-content-between align-items-center">
        <h2>Chi Tiết Máy In: <span class="text-info">{{ $product->name }}</span></h2>
        <a class="btn btn-secondary" href="{{ route('products.index') }}"> Quay Lại</a>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success"><p>{{ $message }}</p></div>
@endif

<!-- PHẦN 1: THÔNG TIN MÁY IN GỐC -->
<div class="row mb-5">
    <div class="col-md-4 text-center">
        @if($product->image)
            <img src="/images/{{ $product->image }}" style="width: 100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        @else
            <div style="width: 100%; height: 250px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                <span class="text-muted">Không có hình ảnh</span>
            </div>
        @endif
    </div>
    <div class="col-md-8">
        <table class="table table-bordered">
            <tbody>
                <tr><th style="width: 30%;">Danh Mục:</th><td><span class="badge badge-info px-3 py-2">{{ $product->category->name ?? 'Không có' }}</span></td></tr>
                <tr><th>Hãng Sản Xuất:</th><td>{{ $product->brand }}</td></tr>
                <tr><th>Loại Máy:</th><td>{{ $product->type }}</td></tr>
                <tr><th>Mô Tả:</th><td>{{ $product->description ?? 'Chưa có mô tả.' }}</td></tr>
            </tbody>
        </table>
    </div>
</div>

<hr>

<!-- PHẦN 2: QUẢN LÝ PHIÊN BẢN (VARIANTS) -->
<div class="row mt-4">
    <div class="col-md-12">
        <h3 class="mb-4" style="font-weight: 700; color: var(--primary-color);">Các Phiên Bản (Màu sắc, Giá)</h3>
        
        <!-- Form thêm phiên bản mới -->
        <div class="card mb-4" style="border-radius: 15px; border: 1px solid #e2e8f0;">
            <div class="card-body bg-light" style="border-radius: 15px;">
                <form action="{{ route('variants.store') }}" method="POST" class="row align-items-end">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div class="col-md-3 form-group mb-0">
                        <small class="font-weight-bold text-uppercase text-secondary">Mã hàng (SKU):</small>
                        <input type="text" name="sku" class="form-control" placeholder="VD: CANON-DEN-01" required>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <small class="font-weight-bold text-uppercase text-secondary">Màu sắc:</small>
                        <input type="text" name="color" class="form-control" placeholder="Đen, Trắng..." required>
                    </div>
                    <div class="col-md-3 form-group mb-0">
                        <small class="font-weight-bold text-uppercase text-secondary">Giá bán (VNĐ):</small>
                        <input type="number" name="price" class="form-control" placeholder="VD: 3500000" required>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <small class="font-weight-bold text-uppercase text-secondary">Tồn kho:</small>
                        <input type="number" name="stock" class="form-control" placeholder="Số lượng" required>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <button type="submit" class="btn btn-success w-100 h-100" style="padding: 13px;">+ Thêm</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng danh sách phiên bản hiện có -->
        <table class="table table-bordered table-hover">
            <thead class="bg-white">
                <tr>
                    <th>Mã SKU</th>
                    <th>Màu Sắc</th>
                    <th>Giá Bán (VNĐ)</th>
                    <th>Tồn Kho</th>
                    <th width="100px">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($product->variants as $variant)
                <tr>
                    <td><strong>{{ $variant->sku }}</strong></td>
                    <td>{{ $variant->color }}</td>
                    <td class="text-danger font-weight-bold">{{ number_format($variant->price, 0, ',', '.') }} đ</td>
                    <td>{{ $variant->stock }} chiếc</td>
                    <td>
                        <form action="{{ route('variants.destroy', $variant->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('Bạn có chắc muốn xóa bản này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Sản phẩm này chưa có phiên bản nào. Hãy thêm ở form phía trên!</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection