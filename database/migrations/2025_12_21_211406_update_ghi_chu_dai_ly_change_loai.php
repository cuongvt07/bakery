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
        // Change 'loai' column from enum to string (varchar) to allow dynamic types
        // Using raw SQL is safer for modifying Enum columns in some databases
        DB::statement("ALTER TABLE ghi_chu_dai_ly MODIFY COLUMN loai VARCHAR(50) COMMENT 'Loại ghi chú (slug)'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original Enum values if needed (warning: data loss for new dynamic types)
        DB::statement("ALTER TABLE ghi_chu_dai_ly MODIFY COLUMN loai ENUM('hop_dong', 'chi_phi', 'cong_an', 'vat_dung', 'bien_bao', 'khac') COMMENT 'Loại ghi chú'");
    }
};
