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
        Schema::table('yeu_cau_ca_lam', function (Blueprint $table) {
            $table->foreignId('diem_ban_id')
                ->nullable()
                ->after('nguoi_dung_id')
                ->constrained('diem_ban')
                ->onDelete('set null');

            $table->index('diem_ban_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yeu_cau_ca_lam', function (Blueprint $table) {
            $table->dropForeign(['diem_ban_id']);
            $table->dropColumn('diem_ban_id');
        });
    }
};
