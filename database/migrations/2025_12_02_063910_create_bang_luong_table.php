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
        Schema::create('bang_luong', function (Blueprint $table) {
            $table->id();
            $table->integer('thang'); // 1-12
            $table->integer('nam');
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            
            $table->decimal('luong_co_ban', 12, 2);
            $table->decimal('so_ngay_cong', 5, 2); // From cham_cong table
            
            // Auto calculated
            $table->decimal('tong_luong', 15, 2);
            
            // Status
            $table->enum('trang_thai', ['chua_chot', 'da_chot', 'da_thanh_toan'])->default('chua_chot');
            $table->datetime('ngay_chot')->nullable();
            $table->datetime('ngay_thanh_toan')->nullable();
            
            $table->text('ghi_chu')->nullable();
            
            $table->timestamps();
            
            $table->unique(['nguoi_dung_id', 'thang', 'nam']);
            $table->index(['thang', 'nam']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bang_luong');
    }
};
