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
        Schema::create('nhat_ky_hoat_dong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->nullable()->constrained('nguoi_dung')->onDelete('set null');
            $table->string('hanh_dong', 100); // "tao_ca_lam", "nhap_kho", "chot_ca"
            $table->text('mo_ta')->nullable();
            $table->json('du_lieu_cu')->nullable(); // Data before change
            $table->json('du_lieu_moi')->nullable(); // Data after change
            $table->string('ip_address', 45)->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('nguoi_dung_id');
            $table->index('hanh_dong');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhat_ky_hoat_dong');
    }
};
