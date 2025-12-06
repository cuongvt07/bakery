<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng: Phân bổ hàng ĐẾN TỪNG ĐIỂM (Tự động từ setting)
        Schema::create('phan_bo_hang_diem_ban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phieu_xuat_hang_tong_id')->nullable()->constrained('phieu_xuat_hang_tong')->onDelete('cascade');
            $table->foreignId('diem_ban_id')->constrained('diem_ban');
            $table->foreignId('nguoi_nhan_id')->nullable()->constrained('nguoi_dung'); // Nhân viên nhận hàng
            
            $table->datetime('ngay_nhan')->nullable(); // Khi nhân viên xác nhận nhận
            $table->enum('trang_thai', ['chua_nhan', 'da_nhan'])->default('chua_nhan');
            
            $table->timestamps();
            $table->index('diem_ban_id');
            $table->index('trang_thai');
        });

        // Bảng: Chi tiết phân bổ (Từng loại bánh cho từng điểm)
        Schema::create('chi_tiet_phan_bo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phan_bo_hang_diem_ban_id')->constrained('phan_bo_hang_diem_ban')->onDelete('cascade');
            $table->foreignId('san_pham_id')->constrained('san_pham');
            $table->decimal('so_luong', 12, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_phan_bo');
        Schema::dropIfExists('phan_bo_hang_diem_ban');
    }
};
