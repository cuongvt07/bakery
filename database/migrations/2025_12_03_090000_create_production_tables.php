<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bảng Công thức Sản xuất
        Schema::create('cong_thuc_san_xuat', function (Blueprint $table) {
            $table->id();
            $table->string('ma_cong_thuc', 50)->unique();
            $table->string('ten_cong_thuc', 200);
            $table->foreignId('san_pham_id')->constrained('san_pham')->onDelete('cascade');
            
            $table->integer('so_luong_san_xuat'); // 1 mẻ = X cái
            $table->string('don_vi_san_xuat', 20)->default('cái');
            
            $table->decimal('chi_phi_uoc_tinh', 12, 2)->default(0); // Tự động tính
            $table->text('mo_ta')->nullable();
            
            $table->enum('trang_thai', ['hoat_dong', 'ngung_su_dung'])->default('hoat_dong');
            
            $table->timestamps();
        });

        // 2. Bảng Chi tiết Công thức (Nguyên liệu)
        Schema::create('chi_tiet_cong_thuc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cong_thuc_id')->constrained('cong_thuc_san_xuat')->onDelete('cascade');
            $table->foreignId('nguyen_lieu_id')->constrained('nguyen_lieu');
            
            $table->decimal('so_luong', 10, 2);
            $table->string('don_vi', 20); // kg, lít, gói
            $table->decimal('don_gia', 12, 2)->default(0); // Giá nguyên liệu
            
            $table->timestamps();
        });

        // 3. Bảng Mẻ Sản xuất
        Schema::create('me_san_xuat', function (Blueprint $table) {
            $table->id();
            $table->string('ma_me', 50)->unique(); // SANG-001, TRUA-002
            
            $table->foreignId('cong_thuc_id')->constrained('cong_thuc_san_xuat');
            $table->foreignId('nguoi_tao_id')->constrained('nguoi_dung');
            
            $table->date('ngay_san_xuat');
            $table->enum('buoi', ['sang', 'trua', 'chieu']);
            
            // Số lượng
            $table->integer('so_luong_du_kien');
            $table->integer('so_luong_that_bai')->default(0); // Sau QC
            $table->integer('so_luong_thuc_te')->default(0); // = Dự kiến - Thất bại
            $table->decimal('ti_le_hong', 5, 2)->default(0); // %
            
            $table->date('han_su_dung')->nullable();
            
            // QC
            $table->json('anh_qc')->nullable(); // Mảng đường dẫn ảnh lỗi
            $table->text('ghi_chu_qc')->nullable();
            $table->foreignId('nguoi_qc_id')->nullable()->constrained('nguoi_dung');
            
            // Trạng thái
            $table->enum('trang_thai', ['ke_hoach', 'dang_san_xuat', 'qc', 'hoan_thanh', 'huy'])->default('ke_hoach');
            
            $table->timestamps();
            
            $table->index('ngay_san_xuat');
            $table->index('trang_thai');
        });

        // 4. Cập nhật bảng Phân bổ để liên kết với Mẻ
        Schema::table('phan_bo_hang_diem_ban', function (Blueprint $table) {
            $table->foreignId('me_san_xuat_id')->nullable()->after('phieu_xuat_hang_tong_id')->constrained('me_san_xuat');
        });
    }

    public function down(): void
    {
        Schema::table('phan_bo_hang_diem_ban', function (Blueprint $table) {
            $table->dropForeign(['me_san_xuat_id']);
            $table->dropColumn('me_san_xuat_id');
        });
        
        Schema::dropIfExists('me_san_xuat');
        Schema::dropIfExists('chi_tiet_cong_thuc');
        Schema::dropIfExists('cong_thuc_san_xuat');
    }
};
