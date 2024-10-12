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
        Schema::table('districts', function (Blueprint $table) {
            $table->integer('district_code')->after('codename');
        });

        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('codename');
            $table->integer('district_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wards');
        Schema::table('districts', function (Blueprint $table) {
            $table->dropColumn('district_code');
        });
    }
};
