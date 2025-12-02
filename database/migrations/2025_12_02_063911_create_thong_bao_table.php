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
        Schema::create('thong_bao', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de', 200);
            $table->text('noi_dung');
            $table->enum('loai_thong_bao', ['he_thong', 'canh_bao', 'thong_tin'])->default('thong_tin');
            
            // Gửi đến
            $table->boolean('gui_toi_tat_ca')->default(false);
            $table->foreignId('diem_ban_id')->nullable()->constrained('diem_ban')->onDelete('cascade');
            $table->foreignId('nguoi_nhan_id')->nullable()->constrained('nguoi_dung')->onDelete('cascade');
            
            $table->foreignId('nguoi_gui_id')->constrained('nguoi_dung');
            $table->datetime('ngay_gui');
            
            $table->timestamps();
            
            $table->index('ngay_gui');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_bao');
    }
};
