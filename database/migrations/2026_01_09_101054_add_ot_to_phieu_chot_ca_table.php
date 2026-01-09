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
            $table->boolean('ot')->default(false)->after('ghi_chu')->comment('Overtime flag - Tăng ca');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phieu_chot_ca', function (Blueprint $table) {
            $table->dropColumn('ot');
        });
    }
};
