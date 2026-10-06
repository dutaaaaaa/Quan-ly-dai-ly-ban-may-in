<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Bổ sung thư viện xử lý File chuẩn Laravel

class ProductController extends Controller
{
    public function index(Request $request) {
        $query = Product::query();

        if ($request->filled('brand')) $query->where('brand', $request->brand);
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('min_price')) $query->whereHas('variants', function($q) use ($request) { $q->where('price', '>=', $request->min_price); });
        if ($request->filled('max_price')) $query->whereHas('variants', function($q) use ($request) { $q->where('price', '<=', $request->max_price); });

        $products = $query->with('variants', 'category')->get();
        $brands = Product::select('brand')->distinct()->pluck('brand');
        $categories = Category::where('status', 1)->get();

        return view('admin.products.index', compact('products', 'brands', 'categories'));
    }

    public function create() {
        $categories = Category::where('status', 1)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request) {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|max:255',
            'brand' => 'required',
            'image' => 'nullable|image|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'required|distinct|unique:product_variants,sku',
            'variants.*.color' => 'required',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ], [
            'variants.required' => 'Bạn phải nhập ít nhất 1 phiên bản màu sắc cho máy in.',
            'variants.*.sku.required' => 'Mã hàng (SKU) không được để trống.',
            'variants.*.sku.distinct' => 'Mã hàng (SKU) trong danh sách không được trùng nhau.',
            'variants.*.sku.unique' => 'Mã hàng (SKU) này đã tồn tại trên hệ thống, vui lòng đổi mã khác.',
            'variants.*.price.min' => 'Giá bán không được phép là số âm.',
            'variants.*.stock.min' => 'Số lượng tồn kho không được phép là số âm.',
        ]);

        $input = $request->except('variants');

        // ĐOẠN SỬA LỖI: Lưu ảnh chuẩn vào thư mục storage/app/public/products
        if ($request->hasFile('image')) {
            // Hàm store tự động tạo tên file ngẫu nhiên siêu bảo mật và lưu vào két sắt
            $path = $request->file('image')->store('products', 'public');
            $input['image'] = $path; // DB sẽ lưu dạng: products/ten_anh.jpg
        }
        
        $product = Product::create($input);

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $product->variants()->create($variant);
            }
        }
        return redirect()->route('products.index')->with('success', 'Đã thêm máy in thành công!');
    }

    public function show(Product $product) { 
        return view('admin.products.show', compact('product')); 
    }

    public function edit(Product $product) {
        $categories = Category::where('status', 1)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product) {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|max:255',
            'brand' => 'required',
            'image' => 'nullable|image|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'required|distinct',
            'variants.*.color' => 'required',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ], [
            'variants.required' => 'Máy in phải có ít nhất 1 phiên bản màu sắc.',
            'variants.*.sku.distinct' => 'Mã hàng (SKU) không được trùng nhau.',
            'variants.*.price.min' => 'Giá bán không được phép là số âm.',
            'variants.*.stock.min' => 'Số lượng tồn kho không được phép là số âm.',
        ]);

        $input = $request->except('variants');

        // ĐOẠN SỬA LỖI & NÂNG CẤP: Cập nhật ảnh chuẩn Laravel
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ đi cho nhẹ máy (nếu có)
            if (!empty($product->image) && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            
            // Lưu ảnh mới vào két sắt
            $path = $request->file('image')->store('products', 'public');
            $input['image'] = $path;
        } else { 
            unset($input['image']); 
        }

        $product->update($input);
        
        $product->variants()->delete();
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $product->variants()->create($variant);
            }
        }
        return redirect()->route('products.index')->with('success', 'Đã cập nhật máy in thành công!');
    }

    public function destroy(Product $product) {
        // Nâng cấp: Xóa máy in thì xóa luôn file ảnh trong ổ cứng
        if (!empty($product->image) && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Đã xóa máy in thành công!');
    }
    // Trang chi tiết sản phẩm dành cho khách hàng
    public function detail($id) {
        $product = Product::with('variants', 'category')->findOrFail($id);
        return view('user.detail', compact('product'));
    }
}