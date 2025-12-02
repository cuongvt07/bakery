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
        Schema::create('nhan_vien_diem_ban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->foreignId('diem_ban_id')->constrained('diem_ban')->onDelete('cascade');
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc')->nullable();
            $table->timestamps();
            $table->unique(['nguoi_dung_id', 'diem_ban_id', 'ngay_bat_dau']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhan_vien_diem_ban');
    }
};
