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
        Schema::create('yeu_cau_ca_lam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->enum('loai_yeu_cau', ['xin_ca', 'doi_ca', 'xin_nghi']);
            
            $table->foreignId('ca_lam_viec_id')->nullable()->constrained('ca_lam_viec')->onDelete('set null');
            $table->date('ngay_mong_muon')->nullable();
            $table->time('gio_bat_dau')->nullable();
            $table->time('gio_ket_thuc')->nullable();
            
            $table->text('ly_do')->nullable();
            $table->enum('trang_thai', ['cho_duyet', 'da_duyet', 'tu_choi'])->default('cho_duyet');
            
            $table->foreignId('nguoi_duyet_id')->nullable()->constrained('nguoi_dung');
            $table->datetime('ngay_duyet')->nullable();
            $table->text('ghi_chu_duyet')->nullable();
            
            $table->timestamps();
            
            $table->index('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_ca_lam');
    }
};
