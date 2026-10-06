<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\ProductVariant;
use App\Services\GHNService;
use App\Services\GHNOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // ==========================================
    // 1. CÁC VIEW HIỂN THỊ ĐƠN HÀNG & THANH TOÁN
    // ==========================================
    public function index()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }
        $totalPrice = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('user.payment.index', compact('cart', 'totalPrice'));
    }

    public function orderHistory()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product'])
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('user.payment.order', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }
        $order->load('items.product');
        return view('user.payment.show', compact('order'));
    }

    // ==========================================
    // 2. AJAX LOCATION & TÍNH PHÍ GHN QUA TOKEN THẬT
    // ==========================================
   public function getProvinces()
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'token' => env('GHN_TOKEN'),
            ])->get(env('GHN_BASE_URL') . '/master-data/province');

            return response()->json($response->json());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi lấy tỉnh thành: ' . $e->getMessage());
            return response()->json(['code' => 500, 'message' => 'Lỗi kết nối GHN']);
        }
    }

    public function getDistricts(int $provinceId, GHNService $ghn)
    {
        return response()->json($ghn->getDistricts($provinceId));
    }

    public function getWards(int $districtId, GHNService $ghn)
    {
        return response()->json($ghn->getWards($districtId));
    }

    public function getShippingFee(Request $request, GHNService $ghn)
    {
        $cart = session('cart', []);
        
        $totalWeight = collect($cart)->sum(
            fn($item) => (int) ($item['weight'] ?? 200) * (int) $item['quantity']
        );

        $res = $ghn->calculateFee([
            'service_type_id' => 2, // Gói chuẩn E-commerce
            'from_district_id' => (int) config('services.ghn.from_district_id', 1450),
            'to_district_id' => (int) $request->to_district_id,
            'to_ward_code' => (string) $request->to_ward_code,
            'weight' => $totalWeight > 0 ? $totalWeight : 300,
            'length' => 15,
            'width' => 15,
            'height' => 10,
        ]);
        
        return response()->json($res);
    }

    // ==========================================
    // 3. XỬ LÝ ĐẶT HÀNG (PROCESS PAYMENT)
    // ==========================================
    public function processPayment(Request $request, GHNService $ghn, GHNOrderService $ghnOrders)
    {
        // ĐÃ TÁCH THÀNH 2 MẢNG: [LUẬT KIỂM TRA], [CÂU THÔNG BÁO]
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'regex:/^0\d{9}$/'], // Bắt buộc số 0 ở đầu và 9 số theo sau
            'address' => 'required|string|max:255',
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
            'payment_method' => 'required|in:cod,momo',
        ], [
            // Dàn thông báo lỗi tiếng Việt xịn sò
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại chỉ được chứa số, bắt đầu bằng số 0 và đủ 10 chữ số.',
            'address.required' => 'Vui lòng nhập địa chỉ cụ thể.',
            'to_district_id.required' => 'Vui lòng chọn Quận/Huyện.',
            'to_ward_code.required' => 'Vui lòng chọn Phường/Xã.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Không thể thanh toán vì giỏ hàng trống.');
        }

        // 1. Tính tổng tiền hàng và tổng khối lượng sản phẩm
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        
        $totalWeight = collect($cart)->sum(
            fn($item) => (int) ($item['weight'] ?? 200) * (int) $item['quantity']
        );

        // 2. Tính lại phí ship chuẩn xác từ GHN trên server
        $feeResponse = $ghn->calculateFee([
            'service_type_id' => 2,
            'from_district_id' => (int) config('services.ghn.from_district_id'),
            'to_district_id' => (int) $request->to_district_id,
            'to_ward_code' => (string) $request->to_ward_code,
            'weight' => $totalWeight > 0 ? $totalWeight : 300,
            'length' => 15,
            'width' => 15,
            'height' => 10,
        ]);

        $shippingFee = (isset($feeResponse['code']) && $feeResponse['code'] == 200)
            ? (int) $feeResponse['data']['total']
            : 0;

        // Tổng thanh toán = Tiền hàng + Phí ship
        $finalTotal = $subtotal + $shippingFee;

        // 3. Tạo đơn hàng và chi tiết đơn hàng trong Database
        $order = DB::transaction(function () use ($request, $shippingFee, $finalTotal, $cart) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'total_price' => $finalTotal,
                'status' => 'pending',
                'to_district_id' => (int) $request->to_district_id,
                'to_ward_code' => (string) $request->to_ward_code,
                'ghn_total_fee' => $shippingFee,
                'shipping_status' => 'pending',
            ]);

            foreach ($cart as $variantId => $item) {
                // Lấy ra biến thể từ Database để truy ngược lại ID sản phẩm gốc
                $variant = ProductVariant::find($variantId);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant ? $variant->product_id : $variantId, 
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
            return $order;
        });

        // Xóa session giỏ hàng
        session()->forget('cart');

        // 4. Phân luồng thanh toán
        if ($request->payment_method === 'momo') {
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway' => 'momo',
                'amount' => $order->total_price,
                'status' => 'pending',
            ]);
            return redirect()->route('user.orders.momo.start', $order);
        }

        PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => 'cod',
            'amount' => $order->total_price,
            'status' => 'pending',
            'message' => 'Thanh toán khi nhận hàng',
        ]);

        // NHÁNH COD: TẠO VẬN ĐƠN GHN NGAY LẬP TỨC
        $order->load('items.product');
        $ghnOrderResponse = $ghnOrders->create($order);

        if (($ghnOrderResponse['code'] ?? null) == 200 && !empty($ghnOrderResponse['data']['order_code'])) {
            $order->update([
                'status' => 'cod_ordered',
                'ghn_order_code' => $ghnOrderResponse['data']['order_code'],
                'shipping_status' => 'ready_to_pick',
            ]);
            return redirect()->route('user.orders.index')
                ->with('success', 'Đặt hàng thành công! Mã vận đơn GHN: ' . $ghnOrderResponse['data']['order_code']);
        }

        Log::error('GHN COD Order Failed: ', $ghnOrderResponse ?? []);
        $order->update(['status' => 'cod_ordered']);
        
        return redirect()->route('user.orders.index')
            ->with('warning', 'Đặt hàng thành công nhưng chưa thể tạo vận đơn GHN tự động.');
    }
}