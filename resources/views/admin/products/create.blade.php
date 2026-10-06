@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Thêm Máy In Mới</h2>
    <a class="btn btn-secondary" href="{{ route('products.index') }}"> Quay Lại</a>
</div>

<!-- HIỂN THỊ CẢNH BÁO LỖI (NẾU NHẬP SAI LOGIC) -->
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

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row bg-white p-4 rounded shadow-sm border mb-4">
        <div class="col-md-6 form-group"><strong>Loại Máy In:</strong><select name="category_id" class="form-control" required><option value="">-- Chọn loại --</option>@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><strong>Tên Máy In:</strong><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
        <div class="col-md-6 form-group"><strong>Hãng Sản Xuất:</strong><select name="brand" class="form-control" required><option value="Canon">Canon</option><option value="HP">HP</option><option value="Brother">Brother</option><option value="Epson">Epson</option><option value="Khác">Khác</option></select></div>
        <div class="col-md-6 form-group"><strong>Hình Ảnh:</strong><input type="file" name="image" class="form-control"></div>
    </div>
    
    <div class="row bg-white p-4 rounded shadow-sm border">
        <div class="col-12 d-flex justify-content-between mb-3 align-items-center"><h4 class="m-0 text-primary">Các Phiên Bản Màu Sắc</h4><button type="button" id="addVariant" class="btn btn-sm btn-info text-white font-weight-bold">+ Thêm màu</button></div>
        <table class="table table-bordered" id="variantsTable">
            <thead class="bg-light"><tr><th>Mã SKU</th><th>Màu Sắc</th><th>Giá Bán (VNĐ)</th><th>Tồn Kho</th><th>Xóa</th></tr></thead>
            <tbody>
                <tr>
                    <td><input type="text" name="variants[0][sku]" class="form-control" placeholder="Mã không trùng lặp" required></td>
                    <td><input type="text" name="variants[0][color]" class="form-control" placeholder="VD: Đen" required></td>
                    <!-- Thêm min="0" để chặn nhập số âm bằng html -->
                    <td><input type="number" name="variants[0][price]" class="form-control" min="0" placeholder="Giá tiền..." required></td>
                    <td><input type="number" name="variants[0][stock]" class="form-control" min="0" placeholder="Số lượng..." required></td>
                    <td><button type="button" class="btn btn-danger remove-tr">X</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-3"><button type="submit" class="btn btn-success px-5 py-3 font-weight-bold">LƯU TẤT CẢ VÀO HỆ THỐNG</button></div>
</form>

<script>
    var i = 0; 
    document.getElementById('addVariant').addEventListener('click', function() { 
        ++i; 
        var newRow = document.getElementById('variantsTable').getElementsByTagName('tbody')[0].insertRow(); 
        newRow.innerHTML = `
            <td><input type="text" name="variants[${i}][sku]" class="form-control" placeholder="Mã không trùng lặp" required></td>
            <td><input type="text" name="variants[${i}][color]" class="form-control" placeholder="VD: Đen" required></td>
            <td><input type="number" name="variants[${i}][price]" class="form-control" min="0" placeholder="Giá tiền..." required></td>
            <td><input type="number" name="variants[${i}][stock]" class="form-control" min="0" placeholder="Số lượng..." required></td>
            <td><button type="button" class="btn btn-danger remove-tr">X</button></td>
        `; 
    });
    document.getElementById('variantsTable').addEventListener('click', function(e) { 
        if(e.target.classList.contains('remove-tr')) e.target.closest('tr').remove(); 
    });
</script>
@endsection