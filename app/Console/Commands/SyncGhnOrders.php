<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncGhnOrders extends Command
{
    protected $signature = 'orders:sync-ghn';
    protected $description = 'Tự động đồng bộ trạng thái đơn hàng từ GHN mỗi giờ';

    public function handle()
    {
        // Chỉ lấy các đơn có mã GHN và chưa hoàn thành/chưa hủy
        $orders = Order::whereNotNull('ghn_order_code')
            ->whereNotIn('shipping_status', ['delivered', 'cancel', 'returned'])
            ->get();

        $token = env('GHN_TOKEN', config('services.ghn.token'));

        foreach ($orders as $order) {
            $response = Http::withHeaders(['Token' => $token])
                ->post('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/detail', [
                    'order_code' => $order->ghn_order_code
                ]);

            if ($response->successful() && $response->json('code') == 200) {
                $ghnStatus = $response->json('data.status');
                $order->shipping_status = $ghnStatus;

                if ($ghnStatus === 'delivered') {
                    $order->status = 'completed'; // Giao xong chốt luôn đơn
                } elseif (in_array($ghnStatus, ['cancel', 'returned'])) {
                    $order->status = 'cancelled';
                }
                
                $order->save();
            }
        }
        $this->info('Đã đồng bộ xong trạng thái GHN!');
    }
}