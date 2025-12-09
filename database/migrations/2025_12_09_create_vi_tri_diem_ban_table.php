<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vi_tri_diem_ban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diem_ban_id')->constrained('diem_ban')->cascadeOnDelete();
            $table->string('ma_vi_tri', 20); // GI1, T2, K5, BG3
            $table->string('ten_vi_tri', 100); // Giỏ 1, Tủ 2, Kệ 5
            $table->text('mo_ta')->nullable(); // Mô tả chi tiết
            $table->string('dia_chi')->nullable(); // 689 Phúc diện
            $table->timestamps();
            
            $table->unique(['diem_ban_id', 'ma_vi_tri']);
        });

        // Add location_id to ghi_chu_dai_ly
        Schema::table('ghi_chu_dai_ly', function (Blueprint $table) {
            $table->foreignId('vi_tri_id')->nullable()->after('loai')->constrained('vi_tri_diem_ban')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ghi_chu_dai_ly', function (Blueprint $table) {
            $table->dropForeign(['vi_tri_id']);
            $table->dropColumn('vi_tri_id');
        });
        
        Schema::dropIfExists('vi_tri_diem_ban');
    }
};
