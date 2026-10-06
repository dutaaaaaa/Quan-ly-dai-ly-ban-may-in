<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Nguoi tao don; xoa user thi xoa don
            $table->string('name'); //
            $table->string('address'); //
            $table->string('phone'); //[cite: 1]
            $table->decimal('total_price', 15, 2); // Tong tien khach phai thanh toan: tien hang + phi ship[cite: 1]
            $table->string('status')->default('pending'); // Trang thai thanh toan/don hang: pending, paid, cod_ordered[cite: 1]
            $table->string('shipping_status')->default('not_shipped'); // Trang thai GHN: pending, ready_to_pick, delivering, delivered...[cite: 1]
            $table->string('ghn_order_code')->nullable()->index(); // Ma van don GHN tra ve sau khi tao don thanh cong[cite: 1]
            $table->integer('ghn_total_fee')->default(0); // Phi van chuyen GHN, tinh bang VND[cite: 1]
            $table->integer('to_district_id')->nullable(); // Ma quan/huyen GHN cua dia chi nguoi nhan[cite: 1]
            $table->string('to_ward_code')->nullable(); // Ma phuong/xa GHN cua dia chi nguoi nhan[cite: 1]
            $table->timestamps(); //[cite: 1]
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};