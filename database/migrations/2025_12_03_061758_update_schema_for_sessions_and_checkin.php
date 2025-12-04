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
        // 1. Update phan_bo_hang_diem_ban
        Schema::table('phan_bo_hang_diem_ban', function (Blueprint $table) {
            $table->enum('buoi', ['sang', 'chieu', 'ca_ngay'])->default('ca_ngay')->after('ngay_nhan');
        });

        // 2. Update ca_lam_viec
        Schema::table('ca_lam_viec', function (Blueprint $table) {
            $table->decimal('tien_mat_dau_ca', 15, 2)->default(0)->after('trang_thai');
            $table->boolean('trang_thai_checkin')->default(false)->after('tien_mat_dau_ca');
            $table->dateTime('thoi_gian_checkin')->nullable()->after('trang_thai_checkin');
        });

        // 3. Create chi_tiet_ca_lam
        Schema::create('chi_tiet_ca_lam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ca_lam_viec_id')->constrained('ca_lam_viec')->onDelete('cascade');
            $table->foreignId('san_pham_id')->constrained('san_pham');
            
            $table->decimal('so_luong_nhan_ca', 12, 2)->default(0); // Opening Stock (Confirmed)
            $table->decimal('so_luong_giao_ca', 12, 2)->default(0); // Closing Stock
            $table->decimal('so_luong_ban', 12, 2)->default(0); // Sold
            
            $table->timestamps();
            
            $table->unique(['ca_lam_viec_id', 'san_pham_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_ca_lam');

        Schema::table('ca_lam_viec', function (Blueprint $table) {
            $table->dropColumn(['tien_mat_dau_ca', 'trang_thai_checkin', 'thoi_gian_checkin']);
        });

        Schema::table('phan_bo_hang_diem_ban', function (Blueprint $table) {
            $table->dropColumn('buoi');
        });
    }
};
