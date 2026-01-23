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
        // Use raw SQL to avoid doctrine/dbal dependency issues
        DB::statement('ALTER TABLE cong_thuc_san_xuat MODIFY COLUMN san_pham_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We cannot easily revert to NOT NULL if there are null values, so we just attempt it
        // but if data exists it might fail. For now, this is acceptable.
        DB::statement('ALTER TABLE cong_thuc_san_xuat MODIFY COLUMN san_pham_id BIGINT UNSIGNED NOT NULL');
    }
};
