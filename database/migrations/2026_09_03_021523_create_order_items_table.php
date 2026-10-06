<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Mỗi item thuộc 1 order[cite: 1]
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Liên kết sản phẩm[cite: 1]
            $table->integer('quantity'); // Số lượng[cite: 1]
            $table->decimal('price', 15, 2); // Giá tại thời điểm mua[cite: 1]
            $table->timestamps(); //[cite: 1]
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};