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
        Schema::table('ca_lam_viec', function (Blueprint $table) {
            // Add column to store total hours worked (in decimal hours, e.g., 3.77 for 3h 46m)
            $table->decimal('tong_gio_lam_viec', 5, 2)->nullable()->default(0)->after('thoi_gian_checkin');
            $table->index('tong_gio_lam_viec');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ca_lam_viec', function (Blueprint $table) {
            $table->dropColumn('tong_gio_lam_viec');
        });
    }
};
