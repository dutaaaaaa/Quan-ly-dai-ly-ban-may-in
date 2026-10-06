<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
        // Lấy tất cả tài khoản, sắp xếp mới nhất lên đầu
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user) {
        // Không cho phép Admin tự xóa chính mình
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
        }
        
        $user->delete();
        return redirect()->back()->with('success', 'Đã xóa tài khoản thành công!');
    }
}