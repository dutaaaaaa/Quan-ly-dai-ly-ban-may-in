@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Sửa Máy In: <span class="text-primary">{{ $product->name }}</span></h2>
    <a class="btn btn-secondary" href="{{ route('products.index') }}"> Quay Lại</a>
</div>

<!-- HIỂN THỊ CẢNH BÁO LỖI -->
@if ($errors->any())
    <div class="alert alert-danger" style="border-radius: 8px;">
        <strong>Cảnh báo!</strong> Vui lòng kiểm tra lại thông tin nhập vào:<br><br>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf 
    @method('PUT')
    
    <div class="row bg-white p-4 rounded shadow-sm border mb-4">
        <div class="col-md-6 form-group">
            <strong>Loại Máy In:</strong>
            <select name="category_id" class="form-control" required>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-6 form-group">
            <strong>Tên Máy In:</strong>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" required>
        </div>
        
        <div class="col-md-6 form-group">
            <strong>Hãng Sản Xuất:</strong>
            <select name="brand" class="form-control" required>
                <option value="Canon" {{ $product->brand == 'Canon' ? 'selected' : '' }}>Canon</option>
                <option value="HP" {{ $product->brand == 'HP' ? 'selected' : '' }}>HP</option>
                <option value="Brother" {{ $product->brand == 'Brother' ? 'selected' : '' }}>Brother</option>
                <option value="Khác" {{ $product->brand == 'Khác' ? 'selected' : '' }}>Khác</option>
            </select>
        </div>
        
        <div class="col-md-6 form-group">
            <strong>Hình Ảnh (Để trống nếu không đổi):</strong>
            <input type="file" name="image" class="form-control">
            @if($product->image)
                {{-- ĐÃ SỬA LẠI ĐƯỜNG DẪN ẢNH --}}
                <img src="{{ asset('storage/' . $product->image) }}" width="60px" class="mt-2 rounded shadow-sm">
            @endif
        </div>
    </div>
    
    <div class="row bg-white p-4 rounded shadow-sm border">
        <div class="col-12 d-flex justify-content-between mb-3 align-items-center">
            <h4 class="text-primary m-0">Quản Lý Các Phiên Bản</h4>
            <button type="button" id="addVariant" class="btn btn-sm btn-info text-white font-weight-bold">+ Thêm màu</button>
        </div>
        
        <table class="table table-bordered" id="variantsTable">
            <thead class="bg-light">
                <tr>
                    <th>SKU</th>
                    <th>Màu Sắc</th>
                    <th>Giá Bán (VNĐ)</th>
                    <th>Tồn Kho</th>
                    <th width="60px">Xóa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($product->variants as $index => $variant)
                <tr>
                    <td>
                        <input type="text" name="variants[{{ $index }}][sku]" value="{{ $variant->sku }}" class="form-control" required>
                    </td>
                    <td>
                        <input type="text" name="variants[{{ $index }}][color]" value="{{ $variant->color }}" class="form-control" required>
                    </td>
                    <td>
                        <input type="number" name="variants[{{ $index }}][price]" value="{{ round($variant->price) }}" class="form-control" min="0" required>
                    </td>
                    <td>
                        <input type="number" name="variants[{{ $index }}][stock]" value="{{ $variant->stock }}" class="form-control" min="0" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-tr"><i class="fa-solid fa-trash"></i> X</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td><input type="text" name="variants[0][sku]" class="form-control" required></td>
                    <td><input type="text" name="variants[0][color]" class="form-control" required></td>
                    <td><input type="number" name="variants[0][price]" class="form-control" min="0" required></td>
                    <td><input type="number" name="variants[0][stock]" class="form-control" min="0" required></td>
                    <td><button type="button" class="btn btn-danger remove-tr">X</button></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-success px-5 py-3 font-weight-bold" style="font-size: 1.1rem;">CẬP NHẬT TẤT CẢ</button>
    </div>
</form>

<script>
    var i = {{ $product->variants->count() > 0 ? $product->variants->count() : 1 }}; 
    
    document.getElementById('addVariant').addEventListener('click', function() { 
        ++i; 
        var newRow = document.getElementById('variantsTable').getElementsByTagName('tbody')[0].insertRow(); 
        newRow.innerHTML = `
            <td><input type="text" name="variants[${i}][sku]" class="form-control" required></td>
            <td><input type="text" name="variants[${i}][color]" class="form-control" required></td>
            <td><input type="number" name="variants[${i}][price]" class="form-control" min="0" required></td>
            <td><input type="number" name="variants[${i}][stock]" class="form-control" min="0" required></td>
            <td><button type="button" class="btn btn-danger remove-tr">X</button></td>
        `; 
    });
    
    document.getElementById('variantsTable').addEventListener('click', function(e) { 
        if(e.target.classList.contains('remove-tr') || e.target.closest('.remove-tr')) {
            e.target.closest('tr').remove(); 
        }
    });
</script>
@endsection