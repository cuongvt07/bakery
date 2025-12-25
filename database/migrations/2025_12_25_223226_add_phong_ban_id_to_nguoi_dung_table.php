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
        Schema::table('nguoi_dung', function (Blueprint $table) {
            // Add phong_ban_id column
            $table->unsignedBigInteger('phong_ban_id')->nullable()->after('vai_tro');
            $table->foreign('phong_ban_id')->references('id')->on('phong_ban')->onDelete('set null');
        });
        
        // Migrate data from loai_nhan_vien to phong_ban_id
        // This assumes we have seeded default departments first
        DB::statement("
            UPDATE nguoi_dung 
            SET phong_ban_id = (
                SELECT id FROM phong_ban WHERE ma_phong_ban = 'PB0001' LIMIT 1
            )
            WHERE loai_nhan_vien = 'ban_hang'
        ");
        
        DB::statement("
            UPDATE nguoi_dung 
            SET phong_ban_id = (
                SELECT id FROM phong_ban WHERE ma_phong_ban = 'PB0002' LIMIT 1
            )
            WHERE loai_nhan_vien = 'san_xuat'
        ");
        
        // Drop old column
        Schema::table('nguoi_dung', function (Blueprint $table) {
            if (Schema::hasColumn('nguoi_dung', 'loai_nhan_vien')) {
                $table->dropColumn('loai_nhan_vien');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            // Re-add loai_nhan_vien
            $table->enum('loai_nhan_vien', ['ban_hang', 'san_xuat'])->default('ban_hang')->after('vai_tro');
        });
        
        // Migrate data back
        DB::statement("
            UPDATE nguoi_dung 
            SET loai_nhan_vien = 'ban_hang'
            WHERE phong_ban_id = (SELECT id FROM phong_ban WHERE ma_phong_ban = 'PB0001' LIMIT 1)
        ");
        
        DB::statement("
            UPDATE nguoi_dung 
            SET loai_nhan_vien = 'san_xuat'
            WHERE phong_ban_id = (SELECT id FROM phong_ban WHERE ma_phong_ban = 'PB0002' LIMIT 1)
        ");
        
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropForeign(['phong_ban_id']);
            $table->dropColumn('phong_ban_id');
        });
    }
};
