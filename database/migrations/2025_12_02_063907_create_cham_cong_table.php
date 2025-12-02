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
        Schema::create('cham_cong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->foreignId('diem_ban_id')->constrained('diem_ban');
            $table->foreignId('ca_lam_viec_id')->nullable()->constrained('ca_lam_viec')->onDelete('set null');
            
            $table->date('ngay_cham');
            
            // Check-in
            $table->time('gio_vao')->nullable();
            $table->decimal('vi_do_vao', 10, 8)->nullable();
            $table->decimal('kinh_do_vao', 11, 8)->nullable();
            $table->string('anh_check_in')->nullable(); // Phase 2
            
            // Check-out
            $table->time('gio_ra')->nullable();
            $table->decimal('vi_do_ra', 10, 8)->nullable();
            $table->decimal('kinh_do_ra', 11, 8)->nullable();
            $table->string('anh_check_out')->nullable(); // Phase 2
            
            $table->decimal('tong_gio_lam', 5, 2)->nullable(); // Auto calculated
            
            $table->timestamps();
            
            $table->index(['nguoi_dung_id', 'ngay_cham']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cham_cong');
    }
};
