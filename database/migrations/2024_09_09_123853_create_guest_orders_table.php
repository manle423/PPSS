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
        Schema::create('guest_orders', function (Blueprint $table) {
            $table->id();
            $table->string('guest_email');
            $table->string('guest_phone_number', 20);
            $table->text('guest_address');
            $table->enum('status', ['PENDING', 'COMPLETED', 'CANCELED', 'SHIPPING'])->default('PENDING'); // Pending là chuẩn bị hàng, Shipping là đang giao hàng, Completed là đã giao hàng, Canceled là đã hủy
            $table->timestamp('order_date')->useCurrent();
            $table->unsignedBigInteger('shipping_method_id');
            $table->enum('payment_method', ['CREDIT_CARD', 'PAYPAL', 'VNPAY']);
            $table->unsignedBigInteger('promotion_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->decimal('total_price', 18, 2);
            $table->decimal('discount_value', 18, 2)->default(0);
            $table->decimal('final_price', 18, 2);
            $table->string('digital_signature');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_orders');
    }
};
