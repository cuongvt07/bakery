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
        Schema::create('ca_lam_viec', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diem_ban_id')->constrained('diem_ban')->onDelete('cascade');
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            
            $table->date('ngay_lam');
            $table->time('gio_bat_dau');
            $table->time('gio_ket_thuc');
            
            $table->enum('trang_thai', ['chua_bat_dau', 'dang_lam', 'da_ket_thuc', 'vang'])
                  ->default('chua_bat_dau');
            $table->text('ghi_chu')->nullable();
            
            $table->timestamps();
            
            $table->index('ngay_lam');
            $table->index('nguoi_dung_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ca_lam_viec');
    }
};
