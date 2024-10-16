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
        Schema::table('addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->after('ward');
            $table->unsignedBigInteger('district_id')->after('province_id');
            $table->dropColumn('province');
            $table->dropColumn('district');
            $table->dropColumn('ward');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('ward', 100)->after('district_id');
            $table->string('province', 100)->after('ward');
            $table->string('district', 100)->after('province');
            $table->dropColumn('province_id');
            $table->dropColumn('district_id');
        });
    }
};
