<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chi_tiet_luan_chuyen', function (Blueprint $table) {
            $table->foreignId('me_san_xuat_id')->nullable()->after('san_pham_id')->constrained('me_san_xuat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_luan_chuyen', function (Blueprint $table) {
            $table->dropForeign(['me_san_xuat_id']);
            $table->dropColumn('me_san_xuat_id');
        });
    }
};
