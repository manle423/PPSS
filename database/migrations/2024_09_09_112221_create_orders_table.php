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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('guest_order_id')->nullable();
            $table->enum('status', ['PENDING', 'COMPLETED', 'CANCELED'])->default('PENDING');
            $table->timestamp('order_date')->useCurrent();
            $table->text('shipping_address')->nullable();
            $table->unsignedBigInteger('shipping_method_id');
            $table->enum('payment_method', ['CREDIT_CARD', 'PAYPAL', 'VNPAY']);
            $table->unsignedBigInteger('promotion_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->decimal('total_price', 18, 2);
            $table->decimal('discount_value', 18, 2)->default(0);
            $table->decimal('final_price', 18, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
