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
        Schema::create('diem_ban', function (Blueprint $table) {
            $table->id();
            $table->string('ma_diem_ban', 20)->unique();
            $table->string('ten_diem_ban', 100);
            $table->text('dia_chi');
            $table->string('so_dien_thoai', 15)->nullable();
            $table->json('thong_tin_vat_dung')->nullable();
            $table->decimal('vi_do', 10, 8)->nullable();
            $table->decimal('kinh_do', 11, 8)->nullable();
            $table->enum('trang_thai', ['hoat_dong', 'dong_cua'])->default('hoat_dong');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diem_ban');
    }
};
