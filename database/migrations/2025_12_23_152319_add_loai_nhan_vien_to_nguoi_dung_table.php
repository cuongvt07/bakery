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
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->enum('loai_nhan_vien', ['ban_hang', 'san_xuat'])->default('ban_hang')->after('vai_tro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
             if (Schema::hasColumn('nguoi_dung', 'loai_nhan_vien')) {
                $table->dropColumn('loai_nhan_vien');
            }
        });
    }
};
