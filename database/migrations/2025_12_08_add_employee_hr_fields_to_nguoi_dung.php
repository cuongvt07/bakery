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
        Schema::table('nguoi_dung', function (Blueprint $table) {
            // Mã nhân viên (unique code)
            $table->string('ma_nhan_vien', 20)->nullable()->unique()->after('id');
            
            // Lương thử việc và chính thức
            $table->decimal('luong_thu_viec', 12, 2)->nullable()->after('luong_co_ban');
            $table->decimal('luong_chinh_thuc', 12, 2)->nullable()->after('luong_thu_viec');
            
            // Ngày ký hợp đồng
            $table->date('ngay_ky_hop_dong')->nullable()->after('ngay_vao_lam');
            
            // Ngày hết hạn hợp đồng
            $table->date('ngay_het_han_hop_dong')->nullable()->after('ngay_ky_hop_dong');
            
            // Loại hợp đồng
            $table->enum('loai_hop_dong', ['thu_viec', 'chinh_thuc', 'hop_tac'])->default('thu_viec')->after('ngay_het_han_hop_dong');
            
            // Facebook profile
            $table->string('facebook')->nullable()->after('email');
            
            // CCCD/CMND
            $table->string('so_cmnd', 20)->nullable()->after('facebook');
            $table->date('ngay_cap_cmnd')->nullable()->after('so_cmnd');
            $table->string('noi_cap_cmnd')->nullable()->after('ngay_cap_cmnd');
            
            // Emergency contact
            $table->string('nguoi_lien_he_khan_cap')->nullable()->after('so_dien_thoai');
            $table->string('sdt_lien_he_khan_cap', 15)->nullable()->after('nguoi_lien_he_khan_cap');
            
            // Bank info for salary
            $table->string('ngan_hang')->nullable();
            $table->string('so_tai_khoan')->nullable();
            $table->string('chu_tai_khoan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn([
                'ma_nhan_vien',
                'luong_thu_viec',
                'luong_chinh_thuc',
                'ngay_ky_hop_dong',
                'ngay_het_han_hop_dong',
                'loai_hop_dong',
                'facebook',
                'so_cmnd',
                'ngay_cap_cmnd',
                'noi_cap_cmnd',
                'nguoi_lien_he_khan_cap',
                'sdt_lien_he_khan_cap',
                'ngan_hang',
                'so_tai_khoan',
                'chu_tai_khoan',
            ]);
        });
    }
};
