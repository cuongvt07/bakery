<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng: Luân chuyển hàng giữa các điểm
        Schema::create('luan_chuyen_hang', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phieu', 50)->unique();
            
            $table->foreignId('diem_ban_nguon_id')->constrained('diem_ban'); // Điểm A
            $table->foreignId('diem_ban_dich_id')->constrained('diem_ban'); // Điểm B
            
            $table->foreignId('nguoi_chuyen_id')->constrained('nguoi_dung');
            $table->foreignId('nguoi_nhan_id')->nullable()->constrained('nguoi_dung');
            
            $table->datetime('ngay_chuyen');
            $table->datetime('ngay_nhan')->nullable();
            
            $table->text('ly_do')->nullable();
            $table->enum('trang_thai', ['dang_chuyen', 'da_nhan', 'huy'])->default('dang_chuyen');
            
            $table->timestamps();
            $table->index('ngay_chuyen');
        });

        // Bảng: Chi tiết luân chuyển
        Schema::create('chi_tiet_luan_chuyen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('luan_chuyen_hang_id')->constrained('luan_chuyen_hang')->onDelete('cascade');
            $table->foreignId('san_pham_id')->constrained('san_pham');
            $table->decimal('so_luong', 12, 2);
            
            // Phase 2: Thêm HSD chi tiết
            $table->date('han_su_dung')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_luan_chuyen');
        Schema::dropIfExists('luan_chuyen_hang');
    }
};
