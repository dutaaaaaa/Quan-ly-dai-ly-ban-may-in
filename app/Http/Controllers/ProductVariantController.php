<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    // Hàm lưu phiên bản mới
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'sku' => 'required|unique:product_variants', // Mã SKU không được trùng
            'color' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer'
        ]);

        ProductVariant::create($request->all());

        return back()->with('success', 'Đã thêm phiên bản thành công!');
    }

    // Hàm xóa phiên bản
    public function destroy(ProductVariant $variant)
    {
        $variant->delete();
        return back()->with('success', 'Đã xóa phiên bản!');
    }
}