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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipping_fee', 18, 2)->nullable();
        });

        Schema::table('guest_orders', function (Blueprint $table) {
            $table->decimal('shipping_fee', 18, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_fee');
        });

        Schema::table('guest_orders', function (Blueprint $table) {
            $table->dropColumn('shipping_fee');
        });
    }
};
