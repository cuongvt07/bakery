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
        Schema::table('phieu_chot_ca', function (Blueprint $table) {
            $table->dropColumn('anh_chot_ket');
            $table->json('anh_tien_mat')->nullable()->after('hang_lech');
            $table->json('anh_hang_hoa')->nullable()->after('anh_tien_mat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phieu_chot_ca', function (Blueprint $table) {
            $table->string('anh_chot_ket')->nullable();
            $table->dropColumn('anh_tien_mat');
            $table->dropColumn('anh_hang_hoa');
        });
    }
};
