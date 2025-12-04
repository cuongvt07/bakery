<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('san_pham', function (Blueprint $table) {
            $table->string('don_vi_phan_phoi')->nullable()->after('don_vi_tinh'); // e.g., Khay
            $table->integer('so_luong_quy_doi')->default(1)->after('don_vi_phan_phoi'); // e.g., 10
        });

        Schema::table('phieu_xuat_hang_tong', function (Blueprint $table) {
            $table->string('ten_me_hang')->nullable()->after('ma_phieu'); // e.g., Sáng, Chiều, Tiếp ứng
        });
    }

    public function down(): void
    {
        Schema::table('san_pham', function (Blueprint $table) {
            $table->dropColumn(['don_vi_phan_phoi', 'so_luong_quy_doi']);
        });

        Schema::table('phieu_xuat_hang_tong', function (Blueprint $table) {
            $table->dropColumn('ten_me_hang');
        });
    }
};
