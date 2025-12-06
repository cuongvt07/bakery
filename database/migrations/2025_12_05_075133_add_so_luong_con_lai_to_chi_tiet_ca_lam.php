<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chi_tiet_ca_lam', function (Blueprint $table) {
            $table->decimal('so_luong_con_lai', 12, 2)->default(0)->after('so_luong_ban')->comment('Available quantity (nhan_ca - ban)');
        });
        
        // Update existing records: so_luong_con_lai = so_luong_nhan_ca - so_luong_ban
        DB::statement('UPDATE chi_tiet_ca_lam SET so_luong_con_lai = so_luong_nhan_ca - so_luong_ban');
    }

    public function down(): void
    {
        Schema::table('chi_tiet_ca_lam', function (Blueprint $table) {
            $table->dropColumn('so_luong_con_lai');
        });
    }
};
