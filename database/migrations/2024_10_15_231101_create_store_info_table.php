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
        Schema::create('store_info', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('footer_why_people_like_us')->nullable();
            $table->string('logo')->nullable();
            $table->string('team')->nullable();
            $table->text('product_category')->nullable();
            $table->text('trusted')->nullable();
            $table->text('quality')->nullable();
            $table->text('price')->nullable();
            $table->text('delivery')->nullable();
            $table->text('thanks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_info');
    }
};
