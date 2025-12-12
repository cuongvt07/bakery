<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nguyen_lieu', function (Blueprint $table) {
            $table->decimal('gia_nhap', 15, 2)->default(0)->after('ton_kho_toi_thieu')->comment('Giá nhập mỗi đơn vị');
        });
    }

    public function down(): void
    {
        Schema::table('nguyen_lieu', function (Blueprint $table) {
            $table->dropColumn('gia_nhap');
        });
    }
};
