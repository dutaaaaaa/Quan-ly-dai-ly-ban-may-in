<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderSuccessMail;
use App\Services\GHNOrderService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }
        $totalPrice = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
return view('user.payment.index', compact('cart', 'totalPrice'));
    }

    public function process(Request $request, GHNOrderService $ghnOrderService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'province_id' => 'required',
            'to_district_id' => 'required',
            'to_ward_code' => 'required',
            'payment_method' => 'required'
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        DB::beginTransaction();
        try {
            // Lấy tổng tiền đã được cộng phí ship từ input ẩn trên giao diện truyền xuống
            $totalPrice = $request->total_price ?? collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

            // 1. Lưu thông tin đơn hàng kèm mã quận/huyện và phường/xã của GHN
            $order = Order::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'total_price' => $totalPrice,
                'status' => 'Chờ xử lý - ' . $request->payment_method,
                'shipping_status' => 'pending_ghn',
                'to_district_id' => $request->to_district_id,
                'to_ward_code' => $request->to_ward_code,
                'payment_method' => $request->input('payment_method', 'cod'),
            ]);

            // 2. Lưu chi tiết sản phẩm
            foreach ($cart as $id => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id, // Hoặc product_variant_id tùy vào cấu trúc bảng của bạn
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            // 3. Đẩy vận đơn sang hệ thống GHN
            $isPaid = ($request->payment_method == 'MOMO');
            $ghnResponse = $ghnOrderService->create($order, $isPaid);

            if (isset($ghnResponse['code']) && $ghnResponse['code'] == 200) {
                $order->update([
                    'ghn_order_code' => $ghnResponse['data']['order_code'],
                    'shipping_status' => 'ready_to_pick'
                ]);
            } else {
                Log::error('Tạo vận đơn GHN thất bại: ' . json_encode($ghnResponse));
            }

            DB::commit();
            session()->forget('cart'); 

            // Gửi mail xác nhận (bắt lỗi ngầm nếu local chưa bật SMTP)
            try {
                $emailTo = Auth::check() ? Auth::user()->email : 'test@gmail.com';
                Mail::to($emailTo)->send(new OrderSuccessMail($order));
            } catch (\Exception $e) { }

            // Nếu chọn MoMo thì chuyển hướng sang cổng thanh toán
            if ($request->payment_method == 'MOMO') {
                return $this->momoPayment($order);
            }

            return redirect()->route('welcome')->with('success', 'Đặt hàng (COD) thành công! Đã tạo mã vận đơn GHN.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi checkout: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    private function momoPayment($order)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = "MOMOBKUN20180529"; 
        $accessKey = "klm05TvNCzjOaHxP"; 
        $secretKey = "at67qH6mk8w5Y1nAmLSbqZgo5q4qQG"; 
        
        $orderInfo = "Thanh toán Web An Tâm Đơn #" . $order->id;
        $amount = (string)$order->total_price;
        $orderId = $order->id . "_" . time();
        $redirectUrl = route('welcome');
        $ipnUrl = route('welcome');
        $extraData = "";
        $requestId = time() . "";
        $requestType = "captureWallet";
        
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen(json_encode($data))));
        $result = curl_exec($ch);
        curl_close($ch);
        
        $jsonResult = json_decode($result, true);
        if (isset($jsonResult['payUrl'])) {
            return redirect($jsonResult['payUrl']);
        }
        
        return redirect()->route('welcome')->with('error', 'Lỗi kết nối Momo.');
    }
}