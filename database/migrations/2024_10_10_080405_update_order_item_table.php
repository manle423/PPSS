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
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('guest_order_id')->nullable()->after('order_id');
            $table->foreign('guest_order_id')->references('id')->on('guest_orders')->onDelete('cascade');
            $table->unsignedBigInteger('order_id')->nullable()->change();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['guest_order_id']);
            $table->dropColumn('guest_order_id');
            $table->dropForeign(['order_id']);
            $table->unsignedBigInteger('order_id')->change();
        });
    }
};
