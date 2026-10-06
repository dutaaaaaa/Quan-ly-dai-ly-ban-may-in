@extends('layouts.app')
@section('content')
<h2 class="mb-4">Thêm Loại Máy In</h2>
<form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row bg-white p-4 rounded shadow-sm border">
        <div class="col-md-6 form-group"><strong>Tên Loại Máy:</strong><input type="text" name="name" class="form-control" required></div>
        <div class="col-md-6 form-group"><strong>Ảnh Đại Diện (Icon):</strong><input type="file" name="image" class="form-control"></div>
        <div class="col-md-12 form-group"><label><input type="checkbox" name="status" value="1" checked> Hiển thị cho khách hàng</label></div>
        <div class="col-md-12 form-group"><strong>Mô Tả:</strong><textarea class="form-control" name="description" rows="3"></textarea></div>
        <div class="col-md-12 text-center mt-3"><button type="submit" class="btn btn-primary px-5 py-2">Lưu</button></div>
    </div>
</form>
@endsection