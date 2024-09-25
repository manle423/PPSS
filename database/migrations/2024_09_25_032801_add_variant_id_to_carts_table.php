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
        Schema::table('carts', function (Blueprint $table) {
            // Add a nullable foreign key to the product_variants table
            $table->foreignId('variant_id')
                  ->nullable()
                  ->constrained('product_variants')
                  ->onDelete('set null'); // Optionally handle cascade behavior
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['variant_id']);
            $table->dropColumn('variant_id');
        });
    }
};
