<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_sales', function (Blueprint $table) {
            $table->enum('phuong_thuc_thanh_toan', ['tien_mat', 'chuyen_khoan'])->default('tien_mat')->after('tong_tien');
        });
    }

    public function down(): void
    {
        Schema::table('pending_sales', function (Blueprint $table) {
            $table->dropColumn('phuong_thuc_thanh_toan');
        });
    }
};
