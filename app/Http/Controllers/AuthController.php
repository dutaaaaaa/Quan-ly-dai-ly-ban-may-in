<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. MỞ FORM ĐĂNG KÝ
    public function showRegister() {
        if (Auth::check()) {
            return Auth::user()->role == 1 
                ? redirect()->route('products.index') 
                : redirect()->route('welcome'); // Đã cập nhật thành 'welcome'
        }
        return view('auth.register');
    }

    // 2. XỬ LÝ ĐĂNG KÝ
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.unique' => 'Email này đã được sử dụng.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 0,
        ]);

        $user->sendEmailVerificationNotification();
        
        Auth::login($user);

        return redirect()->route('verification.notice')->with('success', 'Vui lòng kiểm tra email để xác thực tài khoản.');
    }

    // 3. MỞ FORM ĐĂNG NHẬP
    public function showLogin() {
        if (Auth::check()) {
            return Auth::user()->role == 1 
                ? redirect()->route('products.index') 
                : redirect()->route('welcome'); // Đã cập nhật thành 'welcome'
        }
        return view('auth.login');
    }

    // 4. XỬ LÝ ĐĂNG NHẬP & PHÂN QUYỀN
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Nếu là Admin thì vào trang quản lý sản phẩm
            if (Auth::user()->role == 1) {
                return redirect()->route('products.index');
            }
            // Nếu là User thường thì về trang chủ
            return redirect()->route('welcome'); // Đã cập nhật thành 'welcome'
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
    }

    // 5. XỬ LÝ ĐĂNG XUẤT
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}