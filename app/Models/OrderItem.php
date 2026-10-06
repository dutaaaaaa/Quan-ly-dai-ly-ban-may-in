<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price'
    ]; //[cite: 1]

    // Một mục sản phẩm thuộc về một đơn hàng[cite: 1]
    public function order()
    {
        return $this->belongsTo(Order::class); //[cite: 1]
    }

    // Một mục sản phẩm liên kết với 1 sản phẩm[cite: 1]
    public function product()
    {
        return $this->belongsTo(Product::class); //[cite: 1]
    }
}