@extends('layouts.app')
@section('content')
<div class="row mb-4"><div class="col-12 d-flex justify-content-between align-items-center">
    <h2>Quản Lý Máy In</h2><a class="btn btn-success" href="{{ route('products.create') }}">+ Thêm Máy In</a>
</div></div>

<!-- KHU VỰC BỘ LỌC -->
<div class="card mb-4 border"><div class="card-body bg-light">
    <form action="{{ route('products.index') }}" method="GET" class="row align-items-end">
        <div class="col-md-3 form-group mb-0">
            <small class="font-weight-bold text-secondary">Hãng:</small>
            <select name="brand" class="form-control"><option value="">Tất cả</option>@foreach($brands as $b)<option value="{{ $b }}" {{ request('brand') == $b ? 'selected' : '' }}>{{ $b }}</option>@endforeach</select>
        </div>
        <div class="col-md-3 form-group mb-0">
            <small class="font-weight-bold text-secondary">Loại máy:</small>
            <select name="category_id" class="form-control"><option value="">Tất cả</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select>
        </div>
        <div class="col-md-2 form-group mb-0"><small class="font-weight-bold text-secondary">Giá từ:</small><input type="number" name="min_price" class="form-control" value="{{ request('min_price') }}"></div>
        <div class="col-md-2 form-group mb-0"><small class="font-weight-bold text-secondary">Đến giá:</small><input type="number" name="max_price" class="form-control" value="{{ request('max_price') }}"></div>
        <div class="col-md-2 form-group mb-0 d-flex"><button type="submit" class="btn btn-info w-100 py-2">Lọc Kết Quả</button></div>
    </form>
</div></div>

<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Thông Tin</th>
            <th>Phiên Bản (Màu - Giá - Kho)</th>
            <th width="120px">Thao Tác</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>
                @if($product->image)
                    {{-- ĐÃ SỬA LẠI ĐƯỜNG DẪN ẢNH --}}
                    <img src="{{ asset('storage/' . $product->image) }}" width="80px" class="rounded shadow-sm">
                @else
                    <span class="text-muted">Chưa có ảnh</span>
                @endif
            </td>
            <td>
                <strong>{{ $product->name }}</strong><br>
                <small class="text-secondary">Hãng: <strong>{{ $product->brand }}</strong> | Loại: {{ $product->category->name ?? 'Không rõ' }}</small>
            </td>
            <td>
                @foreach($product->variants as $variant)
                    <div class="mb-1 bg-light p-2 rounded border d-flex justify-content-between align-items-center" style="font-size: 0.9rem;">
                        <span>
                            <strong>{{ $variant->color }}</strong>: 
                            <span class="text-danger font-weight-bold">{{ number_format($variant->price, 0, ',', '.') }} ₫</span> 
                            (Kho: {{ $variant->stock }})
                        </span>
                        @if($variant->stock > 0)
                            <span class="badge badge-success ml-2">Còn</span>
                        @else
                            <span class="badge badge-danger ml-2">Hết</span>
                        @endif
                    </div>
                @endforeach
            </td>
            <td>
                <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                    <a class="btn btn-sm btn-primary w-100 mb-1" href="{{ route('products.edit', $product->id) }}">Sửa</a>
                    @csrf @method('DELETE') 
                    <button type="submit" class="btn btn-sm btn-danger w-100">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection