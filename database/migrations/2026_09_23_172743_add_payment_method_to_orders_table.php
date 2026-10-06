<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        // Thêm cột payment_method, mặc định là 'cod', đặt ngay sau cột 'status' cho dễ quản lý
        $table->string('payment_method')->default('cod')->after('status');
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn('payment_method');
    });
}
};
