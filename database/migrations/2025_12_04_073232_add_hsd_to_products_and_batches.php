<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add HSD to products table
        Schema::table('san_pham', function (Blueprint $table) {
            $table->integer('so_ngay_hsd')->default(3)->after('trang_thai')
                  ->comment('Số ngày hạn sử dụng từ ngày sản xuất');
        });

        // Note: han_su_dung already exists in chi_tiet_me_san_xuat from create table migration
    }

    public function down(): void
    {
        Schema::table('san_pham', function (Blueprint $table) {
            $table->dropColumn('so_ngay_hsd');
        });
    }
};
