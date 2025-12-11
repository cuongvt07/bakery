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
        Schema::create('shift_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nguoi_dung_id');
            $table->unsignedBigInteger('diem_ban_id');
            $table->unsignedBigInteger('shift_template_id')->nullable();
            
            $table->date('ngay_lam'); // Working date
            $table->time('gio_bat_dau'); // Start time
            $table->time('gio_ket_thuc'); // End time
            
            $table->enum('trang_thai', ['pending', 'approved', 'rejected', 'completed'])
                  ->default('pending');
            $table->text('ghi_chu')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('nguoi_dung_id')->references('id')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('diem_ban_id')->references('id')->on('diem_ban')->onDelete('cascade');
            $table->foreign('shift_template_id')->references('id')->on('shift_templates')->onDelete('set null');
            
            // Indexes
            $table->index(['diem_ban_id', 'ngay_lam']);
            $table->index(['nguoi_dung_id', 'trang_thai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_schedules');
    }
};
