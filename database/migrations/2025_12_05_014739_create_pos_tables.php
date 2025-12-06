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
        // 1. Bảng pending_sales - Lưu đơn hàng tạm thời
        Schema::create('pending_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diem_ban_id')->constrained('diem_ban')->onDelete('cascade');
            $table->foreignId('ca_lam_viec_id')->constrained('ca_lam_viec')->onDelete('cascade');
            $table->foreignId('nguoi_ban_id')->constrained('nguoi_dung')->onDelete('cascade');
            
            $table->time('thoi_gian'); // Thời gian tạo đơn
            $table->json('chi_tiet'); // Product details: [{product_id, ten_sp, so_luong, gia, thanh_tien}]
            $table->decimal('tong_tien', 15, 2);
            
            $table->enum('trang_thai', ['pending', 'confirmed', 'cancelled'])->default('pending');
            
            $table->timestamps();
            
            $table->index(['ca_lam_viec_id', 'trang_thai']);
            $table->index('created_at');
        });

        // 2. Bảng batch_ban_hang - Lưu batch đã chốt
        Schema::create('batch_ban_hang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diem_ban_id')->constrained('diem_ban')->onDelete('cascade');
            $table->foreignId('ca_lam_viec_id')->constrained('ca_lam_viec')->onDelete('cascade');
            $table->foreignId('nguoi_chot_id')->constrained('nguoi_dung')->onDelete('cascade');
            
            $table->date('ngay_chot');
            $table->time('gio_chot');
            
            $table->integer('so_don')->default(0); // Số đơn trong batch
            $table->decimal('tong_tien', 15, 2); // Tổng tiền
            
            $table->json('chi_tiet_don'); // Array của pending_sales IDs & details
            
            $table->timestamps();
            
            $table->index(['ca_lam_viec_id', 'ngay_chot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_ban_hang');
        Schema::dropIfExists('pending_sales');
    }
};
