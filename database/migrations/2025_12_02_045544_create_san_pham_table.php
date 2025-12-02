<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('san_pham', function (Blueprint $table) {
            $table->id();
            $table->foreignId('danh_muc_id')->nullable()->constrained('danh_muc_san_pham')->onDelete('set null');
            $table->string('ma_san_pham', 50)->unique();
            $table->string('ten_san_pham', 200);
            $table->text('mo_ta')->nullable();
            $table->string('anh_san_pham')->nullable();
            $table->decimal('gia_ban', 12, 2)->default(0);
            $table->string('don_vi_tinh', 20)->default('cái');
            $table->enum('trang_thai', ['con_hang', 'het_hang', 'ngung_ban'])->default('con_hang');
            $table->timestamps();
            $table->index('danh_muc_id');
            $table->index('trang_thai');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('san_pham');
    }
};
