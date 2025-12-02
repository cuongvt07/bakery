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
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten', 100);
            $table->string('email', 100)->unique();
            $table->string('so_dien_thoai', 15)->nullable();
            $table->string('mat_khau');
            $table->enum('vai_tro', ['admin', 'nhan_vien'])->default('nhan_vien');
            $table->enum('trang_thai', ['hoat_dong', 'khoa'])->default('hoat_dong');
            $table->string('anh_dai_dien')->nullable();
            $table->text('dia_chi')->nullable();
            $table->date('ngay_vao_lam')->nullable();
            $table->decimal('luong_co_ban', 12, 2)->default(0);
            $table->enum('loai_luong', ['theo_ngay', 'theo_gio'])->default('theo_ngay');
            $table->timestamps();
            $table->index('vai_tro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
