<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('user.cart', compact('cart'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $variant = $product->variants()->first();

        // Xử lý nếu sản phẩm lỗi, không có phiên bản
        if (!$variant) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm này chưa có phiên bản!']);
            }
            return redirect()->back()->with('error', 'Sản phẩm này chưa có phiên bản!');
        }

        $cart = session()->get('cart', []);
        $cartKey = $variant->id;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += 1;
        } else {
            $cart[$cartKey] = [
                "name" => $product->name . " (" . $variant->color . ")",
                "price" => $variant->price,
                "image" => $product->image,
                "quantity" => 1,
                "variant_id" => $variant->id
            ];
        }

        session()->put('cart', $cart);

        // Trả về JSON nếu là gọi ngầm AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Đã thêm ' . $product->name . ' vào giỏ hàng thành công!'
            ]);
        }

        // Trả về kèm thông báo flash chuẩn cho form submit thông thường
        return redirect()->back()->with('success', 'Đã thêm ' . $product->name . ' vào giỏ hàng thành công!');
    }

    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }
}