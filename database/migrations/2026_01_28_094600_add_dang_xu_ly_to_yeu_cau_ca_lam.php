<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify enum to add 'dang_xu_ly' status
        DB::statement("ALTER TABLE yeu_cau_ca_lam MODIFY COLUMN trang_thai ENUM('cho_duyet', 'dang_xu_ly', 'da_duyet', 'tu_choi') DEFAULT 'cho_duyet'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        DB::statement("ALTER TABLE yeu_cau_ca_lam MODIFY COLUMN trang_thai ENUM('cho_duyet', 'da_duyet', 'tu_choi') DEFAULT 'cho_duyet'");
    }
};
