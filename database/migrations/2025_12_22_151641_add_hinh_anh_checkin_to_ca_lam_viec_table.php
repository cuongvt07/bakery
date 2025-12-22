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
            $table->text('hinh_anh_checkin')->nullable()->after('tien_mat_dau_ca');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ca_lam_viec', function (Blueprint $table) {
            $table->dropColumn('hinh_anh_checkin');
        });
    }
};
