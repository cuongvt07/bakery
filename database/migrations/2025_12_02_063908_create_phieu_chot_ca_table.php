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
        Schema::create('phieu_chot_ca', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phieu', 50)->unique();
            $table->foreignId('diem_ban_id')->constrained('diem_ban');
            $table->foreignId('nguoi_chot_id')->constrained('nguoi_dung');
            $table->foreignId('ca_lam_viec_id')->nullable()->constrained('ca_lam_viec')->onDelete('set null');
            
            $table->date('ngay_chot');
            $table->time('gio_chot');
            
            // Tiền
            $table->decimal('tien_mat', 15, 2)->default(0);
            $table->decimal('tien_chuyen_khoan', 15, 2)->default(0);
            $table->decimal('tong_tien_thuc_te', 15, 2)->default(0);
            $table->decimal('tong_tien_ly_thuyet', 15, 2)->default(0);
            $table->decimal('tien_lech', 15, 2)->default(0); // Auto calculated
            
            // Hàng hóa (JSON)
            $table->json('ton_dau_ca')->nullable(); // {"san_pham_id": so_luong}
            $table->json('ton_cuoi_ca')->nullable();
            $table->json('hang_lech')->nullable(); // Auto calculated
            
            $table->string('anh_chot_ket')->nullable();
            $table->text('ghi_chu')->nullable();
            
            // Trạng thái duyệt
            $table->enum('trang_thai', ['cho_duyet', 'da_duyet', 'tu_choi'])->default('cho_duyet');
            $table->foreignId('nguoi_duyet_id')->nullable()->constrained('nguoi_dung');
            $table->datetime('ngay_duyet')->nullable();
            
            $table->timestamps();
            
            $table->index('ngay_chot');
            $table->index('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_chot_ca');
    }
};
