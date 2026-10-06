<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            // Khóa ngoại nối với bảng products (Khi xóa sản phẩm thì xóa luôn các phiên bản của nó)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            $table->string('sku')->unique(); // Mã phiên bản (VD: CANON-2900-BLK)
            $table->string('color')->nullable(); // Màu sắc
            $table->decimal('price', 15, 2); // Giá bán
            $table->integer('stock')->default(0); // Số lượng tồn kho
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
