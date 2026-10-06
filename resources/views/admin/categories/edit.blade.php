@extends('layouts.app')
@section('content')
<h2 class="mb-4">Sửa Loại Máy: {{ $category->name }}</h2>
<form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row bg-white p-4 rounded shadow-sm border">
        <div class="col-md-6 form-group"><strong>Tên Loại Máy:</strong><input type="text" name="name" value="{{ $category->name }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><strong>Ảnh:</strong><input type="file" name="image" class="form-control"></div>
        <div class="col-md-12 form-group"><label><input type="checkbox" name="status" value="1" {{ $category->status == 1 ? 'checked' : '' }}> Hiển thị cho khách hàng</label></div>
        <div class="col-md-12 form-group"><strong>Mô Tả:</strong><textarea class="form-control" name="description" rows="3">{{ $category->description }}</textarea></div>
        <div class="col-md-12 text-center mt-3"><button type="submit" class="btn btn-success px-5 py-2">Cập Nhật</button></div>
    </div>
</form>
@endsection