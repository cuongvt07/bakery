<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter the enum to add 'ticket'
        DB::statement("ALTER TABLE yeu_cau_ca_lam MODIFY COLUMN loai_yeu_cau ENUM('xin_ca', 'doi_ca', 'xin_nghi', 'ticket') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE yeu_cau_ca_lam MODIFY COLUMN loai_yeu_cau ENUM('xin_ca', 'doi_ca', 'xin_nghi') NOT NULL");
    }
};
