<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phan_bo_hang_diem_ban', function (Blueprint $table) {
            // Add san_pham_id if not exists (for direct reference)
            if (!Schema::hasColumn('phan_bo_hang_diem_ban', 'san_pham_id')) {
                $table->foreignId('san_pham_id')->after('me_san_xuat_id')->nullable()->constrained('san_pham');
            }
            
            // Add so_luong (quantity) field
            if (!Schema::hasColumn('phan_bo_hang_diem_ban', 'so_luong')) {
                $table->integer('so_luong')->after('san_pham_id')->default(0);
            }
            
            // Add buoi (session) field
            if (!Schema::hasColumn('phan_bo_hang_diem_ban', 'buoi')) {
                $table->enum('buoi', ['sang', 'chieu'])->after('so_luong')->default('sang');
            }
            
            // Add trang_thai (status) field
            if (!Schema::hasColumn('phan_bo_hang_diem_ban', 'trang_thai')) {
                $table->enum('trang_thai', ['chua_nhan', 'da_nhan', 'huy'])->after('buoi')->default('chua_nhan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('phan_bo_hang_diem_ban', function (Blueprint $table) {
            if (Schema::hasColumn('phan_bo_hang_diem_ban', 'trang_thai')) {
                $table->dropColumn('trang_thai');
            }
            if (Schema::hasColumn('phan_bo_hang_diem_ban', 'buoi')) {
                $table->dropColumn('buoi');
            }
            if (Schema::hasColumn('phan_bo_hang_diem_ban', 'so_luong')) {
                $table->dropColumn('so_luong');
            }
            if (Schema::hasColumn('phan_bo_hang_diem_ban', 'san_pham_id')) {
                $table->dropForeign(['san_pham_id']);
                $table->dropColumn('san_pham_id');
            }
        });
    }
};
