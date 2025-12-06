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
        Schema::create('chi_tiet_me_san_xuat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('me_san_xuat_id')->constrained('me_san_xuat')->onDelete('cascade');
            $table->foreignId('cong_thuc_id')->constrained('cong_thuc_san_xuat')->onDelete('cascade');
            $table->foreignId('san_pham_id')->constrained('san_pham')->onDelete('cascade');
            
            $table->integer('so_luong_du_kien')->default(0); // Expected quantity
            $table->integer('so_luong_that_bai')->default(0); // Failed quantity (QC)
            $table->integer('so_luong_thuc_te')->default(0); // Actual quantity (Expected - Failed)
            $table->decimal('ti_le_hong', 5, 2)->default(0); // Failure rate %
            
            $table->date('han_su_dung')->nullable(); // Expiry date
            $table->text('ghi_chu')->nullable();
            
            $table->timestamps();
            
            $table->unique(['me_san_xuat_id', 'san_pham_id']);
            $table->index('han_su_dung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_me_san_xuat');
    }
};
