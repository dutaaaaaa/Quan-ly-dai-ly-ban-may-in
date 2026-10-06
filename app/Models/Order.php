<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'total_price',
        'status',
        'shipping_status',
        'ghn_order_code',
        'ghn_total_fee',
        'to_district_id',
        'to_ward_code',
        'payment_method',
    ];

    // Một đơn hàng thuộc về một người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Dành cho các chức năng cũ gọi bằng $order->items
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // Dành riêng cho trang Admin gọi bằng $order->orderItems
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // Liên kết với bảng giao dịch thanh toán (MoMo/VNPAY)
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}