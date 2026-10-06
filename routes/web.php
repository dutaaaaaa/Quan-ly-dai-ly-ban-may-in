<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductVariantController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\Product; 
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController; 
use App\Http\Controllers\User\MomoController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\GHNWebhookController;
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\UserController as AdminUserController; 
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\FinanceController;
// ==========================================
// 1. ROUTE GỐC (TRANG CHỦ MỞ CHO TẤT CẢ)
// ==========================================
Route::get('/', function () {
    // Truy vấn lấy danh sách sản phẩm mới nhất từ cơ sở dữ liệu (giới hạn 8 cái)
    $products = Product::latest()->take(8)->get(); 
    return view('user.welcome', compact('products')); 
})->name('welcome');

Route::get('/san-pham/{id}', [ProductController::class, 'detail'])->name('product.detail');

// ==========================================
// 2. ROUTE ĐĂNG NHẬP / ĐĂNG KÝ
// ==========================================
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 3. ROUTE XÁC THỰC EMAIL (Chuẩn Lab 3)
// ==========================================
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('welcome')->with('success', 'Xác thực tài khoản thành công!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Đã gửi lại link xác thực vào email của bạn!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ==========================================
// 4. ROUTE KHÁCH HÀNG ĐÃ ĐĂNG NHẬP (Bảo vệ Giỏ hàng & Thanh toán)
// ==========================================
Route::middleware(['auth'])->group(function () {
    // Nhóm Route Giỏ hàng
    Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/gio-hang/them/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/gio-hang/xoa/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Nhóm Route Thanh toán cũ
    Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/thanh-toan/xu-ly', [CheckoutController::class, 'process'])->name('checkout.process');
});

    Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/transactions', [FinanceController::class, 'transactions'])->name('finance.transactions');
    Route::patch('/finance/{order}/status', [FinanceController::class, 'updateStatus'])->name('finance.update-status');
});
// ==========================================
// 5. ROUTE QUẢN TRỊ VIÊN (ADMIN)
// ==========================================
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{id}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle');
    Route::resource('products', ProductController::class);
    Route::resource('variants', ProductVariantController::class);
    Route::post('orders/{id}/sync-ghn', [\App\Http\Controllers\Admin\OrderController::class, 'syncGhn'])->name('orders.sync_ghn');
    Route::resource('users', AdminUserController::class)->names('admin.users');

    // ROUTE CHAT CHO ADMIN
    Route::get('/chat/users', [AdminChatController::class, 'getUsers'])->name('admin.chat.users');
    Route::get('/chat/messages/{userId}', [AdminChatController::class, 'getMessages'])->name('admin.chat.messages');
    Route::post('/chat/send/{userId}', [AdminChatController::class, 'send'])->name('admin.chat.send');
    Route::resource('orders', AdminOrderController::class)->names('admin.orders')->except(['update']);
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/charts', [ReportController::class, 'charts'])->name('admin.reports.charts');
});

// ==========================================
// 6. ROUTE AJAX ĐỊA CHỈ & TÍNH PHÍ GHN
// ==========================================
Route::prefix('locations')->name('locations.')->group(function () {
    Route::get('/provinces', [OrderController::class, 'getProvinces'])->name('provinces');
    Route::get('/districts/{provinceId}', [OrderController::class, 'getDistricts'])->name('districts');
    Route::get('/wards/{districtId}', [OrderController::class, 'getWards'])->name('wards');
    Route::post('/calculate-fee', [OrderController::class, 'getShippingFee'])->name('fee');
});

// ==========================================
// 7. ROUTE XỬ LÝ THANH TOÁN VỚI MOMO & GHN
// ==========================================
// Sửa đường dẫn thành /api/ghn/webhook và tắt CSRF để Postman bắn không bị lỗi 419
Route::post('/api/ghn/webhook', [GHNWebhookController::class, 'handle'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('ghn.webhook');

// Tắt CSRF cho MoMo IPN để máy chủ MoMo gọi về không bị chặn
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('payment.momo.ipn');
    
Route::get('/payment/momo/callback', [MomoController::class, 'callback'])->name('user.payment.momo.callback');

Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    // Payment & Orders
    Route::get('/payment', [OrderController::class, 'index'])->name('payment.index');
    Route::post('/payment/process', [OrderController::class, 'processPayment'])->name('payment.process');
    Route::get('/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])->name('orders.momo.pay');
    Route::get('/orders/{order}/start-momo', [MomoController::class, 'start'])->name('orders.momo.start');
    Route::get('/orders', [OrderController::class, 'orderHistory'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // ROUTE CHAT CHO KHÁCH HÀNG
    Route::post('/chat/send', [UserChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/messages', [UserChatController::class, 'getMessages'])->name('chat.messages');
});