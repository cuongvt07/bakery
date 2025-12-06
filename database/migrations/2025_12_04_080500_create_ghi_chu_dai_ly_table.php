<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ghi_chu_dai_ly', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diem_ban_id')->constrained('diem_ban')->onDelete('cascade');
            
            // Phân loại
            $table->enum('loai', ['hop_dong', 'chi_phi', 'cong_an', 'vat_dung', 'bien_bao', 'khac'])
                  ->comment('Loại ghi chú');
            
            // Nội dung
            $table->string('tieu_de', 200)->comment('Tiêu đề ghi chú');
            $table->text('noi_dung')->nullable()->comment('Mô tả chi tiết');
            
            // Flexible data (JSON)
            $table->json('metadata')->nullable()
                  ->comment('Dữ liệu linh hoạt: {chu_nha, sdt, so_tien, chu_ky, so_dien, so_nuoc...}');
            
            // Images (JSON array of paths)
            $table->text('hinh_anh')->nullable()
                  ->comment('Mảng đường dẫn ảnh JSON');
            
            // Reminders
            $table->date('ngay_nhac_nho')->nullable()
                  ->comment('Ngày cần nhắc nhở');
            $table->boolean('da_xu_ly')->default(false)
                  ->comment('Đã xử lý chưa');
            
            // Priority for dashboard
            $table->enum('muc_do_quan_trong', ['thap', 'trung_binh', 'cao', 'khan_cap'])
                  ->default('trung_binh')
                  ->comment('Mức độ ưu tiên hiển thị');
            
            // Audit
            $table->foreignId('nguoi_tao_id')->nullable()->constrained('nguoi_dung');
            $table->timestamps();
            
            // Indexes
            $table->index('diem_ban_id');
            $table->index('loai');
            $table->index('ngay_nhac_nho');
            $table->index('da_xu_ly');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ghi_chu_dai_ly');
    }
};
