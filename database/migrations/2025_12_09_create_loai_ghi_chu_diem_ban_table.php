<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loai_ghi_chu_diem_ban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diem_ban_id')->nullable()->constrained('diem_ban')->cascadeOnDelete();
            $table->string('ma_loai', 50); // 'vat_dung', 'hop_dong', 'chi_phi'
            $table->string('ten_hien_thi', 100); // 'Vật dụng', 'Hợp đồng'
            $table->string('icon', 10)->default('📝'); // Emoji icon
            $table->string('mau_sac', 20)->default('gray'); // blue, red, green, yellow
            $table->boolean('la_mac_dinh')->default(false); // true for 'vat_dung'
            $table->boolean('hien_thi')->default(true);
            $table->integer('thu_tu')->default(0);
            $table->timestamps();
            
            // Each agency can have unique type codes
            $table->unique(['diem_ban_id', 'ma_loai']);
        });

        // Update ghi_chu_dai_ly to use varchar instead of hardcoded enum
        // NOTE: Commented out for now - keeping ENUM, will add custom types outside enum values
        // Schema::table('ghi_chu_dai_ly', function (Blueprint $table) {
        //     DB::statement("ALTER TABLE ghi_chu_dai_ly MODIFY loai VARCHAR(50)");
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('loai_ghi_chu_diem_ban');
    }
};
