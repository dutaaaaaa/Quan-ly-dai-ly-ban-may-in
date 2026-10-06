@extends('layouts.app')
@section('content')
<div class="row mb-4"><div class="col-12 d-flex justify-content-between align-items-center">
    <h2>Quản Lý Loại Máy In</h2><a class="btn btn-success" href="{{ route('categories.create') }}">+ Thêm Loại Máy</a>
</div></div>
<table class="table table-bordered table-hover">
    <thead><tr><th>ID</th><th>Ảnh</th><th>Tên Loại Máy</th><th>Mô Tả</th><th>Trạng Thái</th><th>Thao Tác</th></tr></thead>
    <tbody>
        @foreach ($categories as $category)
        <tr>
            <td>{{ $category->id }}</td>
            <td>@if($category->image)<img src="/images/categories/{{ $category->image }}" width="60px">@endif</td>
            <td><strong>{{ $category->name }}</strong></td>
            <td>{{ $category->description }}</td>
            <td>@if($category->status == 1) <span class="badge badge-success px-2">Đang Hiện</span> @else <span class="badge badge-secondary px-2">Đang Ẩn</span> @endif</td>
            <td>
                <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                    <a class="btn btn-sm btn-primary" href="{{ route('categories.edit', $category->id) }}">Sửa</a>
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection