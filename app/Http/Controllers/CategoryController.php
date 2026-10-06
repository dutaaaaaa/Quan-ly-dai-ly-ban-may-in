<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::all();
        // Đã trỏ vào thư mục admin
        return view('admin.categories.index', compact('categories'));
    }

    public function create() {
        return view('admin.categories.create');
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required', 'image' => 'nullable|image|max:2048']);
        $input = $request->all();
        $input['status'] = $request->has('status') ? 1 : 0;
        
        if ($image = $request->file('image')) {
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move(public_path('images/categories/'), $profileImage);
            $input['image'] = "$profileImage";
        }
        
        Category::create($input);
        return redirect()->route('categories.index')->with('success', 'Đã thêm Loại Máy In thành công!');
    }

    public function edit(Category $category) {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category) {
        $request->validate(['name' => 'required', 'image' => 'nullable|image|max:2048']);
        $input = $request->all();
        $input['status'] = $request->has('status') ? 1 : 0;
        
        if ($image = $request->file('image')) {
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move(public_path('images/categories/'), $profileImage);
            $input['image'] = "$profileImage";
        } else {
            unset($input['image']);
        }
        
        $category->update($input);
        return redirect()->route('categories.index')->with('success', 'Đã cập nhật Loại Máy In thành công!');
    }

    public function destroy(Category $category) {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Đã xóa Loại Máy In thành công!');
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->status = !$category->status; 
        $category->save();

        return redirect()->back()->with('success', 'Đã thay đổi trạng thái thành công!');
    }
}