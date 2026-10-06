<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order; 
use Illuminate\Support\Facades\Log;

class GHNWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            // Lấy dữ liệu GHN gửi sang (từ Postman)
            $ghnOrderCode = $request->input('OrderCode');
            $ghnStatus = $request->input('Status');

            if (!$ghnOrderCode || !$ghnStatus) {
                return response()->json(['message' => 'Thiếu OrderCode hoặc Status'], 400);
            }

            // Tìm đơn hàng theo mã vận đơn của GHN
            $order = Order::where('ghn_order_code', $ghnOrderCode)->first();

            if ($order) {
                // 1. Luôn cập nhật trạng thái vận chuyển
                $order->shipping_status = $ghnStatus;

                // 2. Logic đồng bộ trạng thái chính của đơn hàng
                if ($ghnStatus === 'delivered') {
                    // Nếu giao thành công -> Chốt đơn hoàn thành
                    $order->status = 'completed'; 

                } elseif ($ghnStatus === 'cancelled' || $ghnStatus === 'return' || $ghnStatus === 'returned') {
                    // Nếu shipper báo hủy hoặc hoàn hàng -> Đơn bị hủy
                    $order->status = 'cancelled';

                } else {
                    // Các trường hợp còn lại: ready_to_pick, picking, picked, delivering...
                    // Đơn hàng vẫn đang trên đường đi, phải giữ ở trạng thái "Đã thanh toán".
                    // Nếu trước đó đang là 'completed' (do bạn test lùi trạng thái), khôi phục lại trạng thái thanh toán ban đầu.
                    if ($order->status === 'completed') {
                        // Tùy theo phương thức lúc khách đặt để lùi về đúng chữ
                        $order->status = ($order->payment_method === 'momo') ? 'paid_momo' : 'paid';
                    }
                }

                $order->save();
                
                Log::info("Webhook GHN: Đã cập nhật đơn {$order->id} sang trạng thái {$ghnStatus}");
                return response()->json(['message' => 'Cập nhật trạng thái GHN thành công'], 200);
            }

            return response()->json(['message' => 'Không tìm thấy mã đơn hàng này trong hệ thống'], 404);

        } catch (\Exception $e) {
            Log::error('Lỗi Webhook GHN: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }
}