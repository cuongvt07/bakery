<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng: Phiếu xuất hàng TỔNG từ xưởng (MỖI NGÀY 1 PHIẾU)
        Schema::create('phieu_xuat_hang_tong', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phieu', 50)->unique(); // PXT-20241202
            $table->foreignId('nguoi_xuat_id')->constrained('nguoi_dung'); // Admin xuất
            
            $table->date('ngay_xuat');
            $table->time('gio_xuat');
            
            // Ảnh chụp toàn bộ hàng xuất trong ngày
            $table->string('anh_hang_xuat')->nullable();
            
            $table->decimal('tong_so_luong', 12, 2)->default(0); // Tổng số bánh xuất
            $table->text('ghi_chu')->nullable();
            
            $table->enum('trang_thai', ['dang_chuan_bi', 'da_xuat', 'huy'])->default('dang_chuan_bi');
            
            $table->timestamps();
            $table->index('ngay_xuat');
        });

        // Bảng: Chi tiết phiếu xuất TỔNG (Từng loại bánh)
        Schema::create('chi_tiet_phieu_xuat_tong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phieu_xuat_hang_tong_id')->constrained('phieu_xuat_hang_tong')->onDelete('cascade');
            $table->foreignId('san_pham_id')->constrained('san_pham');
            $table->decimal('so_luong', 12, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_phieu_xuat_tong');
        Schema::dropIfExists('phieu_xuat_hang_tong');
    }
};
