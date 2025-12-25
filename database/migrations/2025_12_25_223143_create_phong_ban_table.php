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
        Schema::create('phong_ban', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phong_ban', 20)->unique()->comment('Mã phòng ban (auto-generated)');
            $table->string('ten_phong_ban', 100)->comment('Tên phòng ban');
            $table->string('ma_mau', 7)->default('#3B82F6')->comment('Mã màu hex (ví dụ: #FF5733)');
            $table->enum('trang_thai', ['hoat_dong', 'tam_ngung'])->default('hoat_dong');
            $table->timestamps();
            
            $table->index('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phong_ban');
    }
};
