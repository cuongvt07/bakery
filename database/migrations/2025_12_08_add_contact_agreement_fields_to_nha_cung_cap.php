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
        Schema::table('nha_cung_cap', function (Blueprint $table) {
            // Người đại diện liên hệ
            $table->string('nguoi_dai_dien')->nullable()->after('ten_ncc');
            
            // Số điện thoại Zalo (riêng, có thể khác SĐT thường)
            $table->string('so_dien_thoai_zalo', 15)->nullable()->after('so_dien_thoai');
            
            // Sản phẩm cung cấp (text, có thể list ngắn gọn)
            $table->text('san_pham_cung_cap')->nullable()->after('email');
            
            // Các nội dung thỏa thuận (điều khoản, giá, thời gian giao hàng, etc.)
            $table->text('noi_dung_thoa_thuan')->nullable()->after('san_pham_cung_cap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nha_cung_cap', function (Blueprint $table) {
            $table->dropColumn([
                'nguoi_dai_dien',
                'so_dien_thoai_zalo',
                'san_pham_cung_cap',
                'noi_dung_thoa_thuan',
            ]);
        });
    }
};
