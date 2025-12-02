<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nguyen_lieu', function (Blueprint $table) {
            $table->id();
            $table->string('ma_nguyen_lieu', 50)->unique();
            $table->string('ten_nguyen_lieu', 200);
            $table->string('don_vi_tinh', 20);
            $table->decimal('ton_kho_hien_tai', 12, 2)->default(0);
            $table->decimal('ton_kho_toi_thieu', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguyen_lieu');
    }
};
