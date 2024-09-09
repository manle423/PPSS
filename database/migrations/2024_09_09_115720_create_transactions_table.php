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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('guest_order_id')->nullable();
            $table->enum('payment_method', ['CREDIT_CARD', 'PAYPAL', 'E-WALLET']);
            $table->timestamp('transaction_date');
            $table->decimal('amount', 18, 2);
            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};