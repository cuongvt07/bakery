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
        Schema::create('lich_su_cap_nhat_me', function (Blueprint $table) {
            $table->id();
            $table->foreignId('me_san_xuat_id')->constrained('me_san_xuat')->onDelete('cascade');
            $table->foreignId('san_pham_id')->constrained('san_pham');
            $table->foreignId('diem_ban_id')->constrained('diem_ban');
            $table->foreignId('nguoi_cap_nhat_id')->nullable()->constrained('nguoi_dung');
            
            $table->integer('so_luong_doi')->default(0); // +2, -1
            $table->integer('du_lieu_cu')->default(0); // before
            $table->integer('du_lieu_moi')->default(0); // after
            
            $table->text('ghi_chu')->nullable();
            
            $table->timestamps();
            
            $table->index('me_san_xuat_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_cap_nhat_me');
    }
};
