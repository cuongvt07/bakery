<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nha_cung_cap', function (Blueprint $table) {
            $table->id();
            $table->string('ma_ncc', 20)->unique();
            $table->string('ten_ncc', 200);
            $table->string('so_dien_thoai', 15)->nullable();
            $table->text('dia_chi')->nullable();
            $table->string('email', 100)->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nha_cung_cap');
    }
};
