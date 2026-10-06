<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng (Có kèm lọc trạng thái và phương thức thanh toán)
    public function index(Request $request)
    {
        // Bắt đầu câu truy vấn
        $query = Order::with('user')->orderBy('id', 'desc');

        // Kiểm tra xem trên URL có tham số 'status' gửi lên không
        if ($request->filled('status')) {
            // Nếu admin chọn lọc MoMo
            if ($request->status === 'momo') {
                $query->where('payment_method', 'momo');
            } else {
                // Nếu lọc theo các trạng thái thông thường (pending, completed,...)
                $query->where('status', $request->status);
            }
        }

        // Phân trang và giữ nguyên tham số lọc trên URL khi bấm sang trang 2, 3...
        $orders = $query->paginate(10)->withQueryString(); 

        return view('admin.orders.index', compact('orders'));
    }

    // Xem chi tiết đơn hàng (Nếu cần mở rộng)
    public function show($id)
    {
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    // Xóa đơn hàng
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete(); // Database có cascade sẽ tự xóa order_items
            return redirect()->route('admin.orders.index')->with('success', 'Xóa đơn hàng thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.orders.index')->with('error', 'Không thể xóa! Đơn hàng này đang có dữ liệu liên kết.');
        }
    }
    
    // Đồng bộ trạng thái giao hàng từ GHN
    public function syncGhn($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->ghn_order_code) {
            return redirect()->back()->with('error', 'Đơn hàng này chưa có mã vận đơn GHN, không thể đồng bộ!');
        }

        // Gọi API của GHN để tra cứu trạng thái đơn hàng (nhớ đảm bảo env có GHN_TOKEN)
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Token' => env('GHN_TOKEN', config('services.ghn.token')) 
        ])->post('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail', [
            'order_code' => $order->ghn_order_code
        ]);

        if ($response->successful() && $response->json('code') == 200) {
            $ghnStatus = $response->json('data.status'); // GHN trả về: ready_to_pick, picking, delivering, delivered...
            
            // Cập nhật trạng thái vận chuyển
            $order->shipping_status = $ghnStatus;

            // Nếu GHN báo đã giao thành công (delivered), tự động chốt đơn thành hoàn thành
            if ($ghnStatus === 'delivered') {
                $order->status = 'completed';
            }

            // Nếu GHN báo đơn bị hủy hoặc hoàn trả
            if (in_array($ghnStatus, ['cancel', 'returned'])) {
                $order->status = 'cancelled';
            }

            $order->save();

            return redirect()->back()->with('success', 'Đã đồng bộ trạng thái mới nhất từ hệ thống GHN!');
        }

        return redirect()->back()->with('error', 'Không thể kết nối với hệ thống GHN lúc này.');
    }
}