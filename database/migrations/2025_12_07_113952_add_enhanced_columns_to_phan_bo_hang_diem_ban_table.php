<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phan_bo_hang_diem_ban', function (Blueprint $table) {
            // Creator & Approver tracking
            $table->foreignId('nguoi_tao_id')->nullable()->after('nguoi_nhan_id')->constrained('nguoi_dung')->comment('Người tạo phân bổ');
            $table->foreignId('nguoi_duyet_id')->nullable()->after('nguoi_tao_id')->constrained('nguoi_dung')->comment('Người duyệt');
            $table->datetime('ngay_duyet')->nullable()->after('nguoi_duyet_id')->comment('Ngày duyệt');
            
            // Distribution type
            $table->enum('loai_phan_bo', ['tu_me_sx', 'tu_do', 'tu_mau'])->default('tu_me_sx')->after('ngay_duyet')->comment('Loại phân bổ');
            
            // Notes
            $table->text('ghi_chu')->nullable()->after('loai_phan_bo')->comment('Ghi chú');
            
            // Soft delete
            $table->softDeletes()->after('updated_at');
            
            // Indexes for performance
            $table->index('nguoi_tao_id');
            $table->index('loai_phan_bo');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('phan_bo_hang_diem_ban', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'nguoi_tao_id',
                'nguoi_duyet_id', 
                'ngay_duyet',
                'loai_phan_bo',
                'ghi_chu'
            ]);
        });
    }
};
