<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('gateway_order_id');
            $table->integer('result_code')->nullable()->after('status');
            $table->text('request_payload')->nullable()->after('message');
            $table->text('response_payload')->nullable()->after('request_payload');
            $table->timestamp('paid_at')->nullable()->after('response_payload');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_id',
                'result_code',
                'request_payload',
                'response_payload',
                'paid_at'
            ]);
        });
    }
};