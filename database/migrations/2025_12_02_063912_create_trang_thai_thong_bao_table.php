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
        Schema::create('trang_thai_thong_bao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thong_bao_id')->constrained('thong_bao')->onDelete('cascade');
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->boolean('da_doc')->default(false);
            $table->datetime('ngay_doc')->nullable();
            
            $table->timestamps();
            
            $table->unique(['thong_bao_id', 'nguoi_dung_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trang_thai_thong_bao');
    }
};
