<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('chi_tiet_ca_lam', 'so_luong_con_lai')) {
            Schema::table('chi_tiet_ca_lam', function (Blueprint $table) {
                $table->dropColumn('so_luong_con_lai');
            });
        }
    }

    public function down(): void
    {
        // No need to re-add this column - use accessor instead
    }
};
