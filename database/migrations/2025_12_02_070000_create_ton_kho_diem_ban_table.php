<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng: Tồn kho tại điểm bán (Theo ngày)
        Schema::create('ton_kho_diem_ban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diem_ban_id')->constrained('diem_ban')->onDelete('cascade');
            $table->foreignId('san_pham_id')->constrained('san_pham');
            $table->date('ngay');
            
            $table->decimal('ton_dau_ca', 12, 2)->default(0);
            $table->decimal('ton_cuoi_ca', 12, 2)->default(0);
            
            // Phase 2: Thêm HSD
            $table->date('han_su_dung')->nullable();
            
            $table->timestamps();
            
            $table->unique(['diem_ban_id', 'san_pham_id', 'ngay'], 'unique_ton_kho');
            $table->index('ngay');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ton_kho_diem_ban');
    }
};
