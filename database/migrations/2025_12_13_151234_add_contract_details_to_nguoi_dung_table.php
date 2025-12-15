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
            // Ngày thử việc + chính thức
            $table->date('ngay_thu_viec')->nullable()->after('ngay_vao_lam');
            $table->date('ngay_chinh_thuc')->nullable()->after('ngay_thu_viec');
            
            // Ghi chú hợp đồng
            $table->text('ghi_chu_hop_dong')->nullable()->after('loai_hop_dong');
            
            // File hợp đồng (path)
            $table->string('file_hop_dong')->nullable()->after('ghi_chu_hop_dong');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn(['ngay_thu_viec', 'ngay_chinh_thuc', 'ghi_chu_hop_dong', 'file_hop_dong']);
        });
    }
};
