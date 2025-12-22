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
        Schema::table('phan_bo_hang_diem_ban', function (Blueprint $table) {
            // Drop foreign key first if it exists. 
            // We use array syntax for dropForeign which automatically generates the index name
            $table->dropForeign(['phieu_xuat_hang_tong_id']);
            
            // Then drop the column
            $table->dropColumn('phieu_xuat_hang_tong_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phan_bo_hang_diem_ban', function (Blueprint $table) {
            $table->foreignId('phieu_xuat_hang_tong_id')->nullable()->constrained('phieu_xuat_hang_tong')->onDelete('cascade');
        });
    }
};
