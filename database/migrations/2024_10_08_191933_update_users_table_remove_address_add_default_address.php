<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Xóa cột address
            $table->dropColumn('address');

            // Thêm cột default_address_id
            $table->unsignedBigInteger('default_address_id')->nullable();

            // Tạo foreign key đến bảng addresses
            $table->foreign('default_address_id')
                ->references('id')
                ->on('addresses')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Xóa foreign key và cột default_address_id
            $table->dropForeign(['default_address_id']);
            $table->dropColumn('default_address_id');

            // Thêm lại cột address
            $table->text('address')->nullable();
        });
    }
};
