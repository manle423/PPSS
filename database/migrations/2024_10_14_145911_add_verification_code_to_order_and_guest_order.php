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
            $table->string('verification_code')->nullable();
        });

        Schema::table('guest_orders', function (Blueprint $table) {
            $table->string('verification_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('verification_code');
        });

        Schema::table('guest_orders', function (Blueprint $table) {
            $table->dropColumn('verification_code');
        });
    }
};
