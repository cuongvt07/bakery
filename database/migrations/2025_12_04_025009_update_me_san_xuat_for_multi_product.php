<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only migrate data if there are existing batches with recipes
        $hasData = DB::table('me_san_xuat')
            ->whereNotNull('cong_thuc_id')
            ->exists();
            
        if ($hasData) {
            // Migrate existing data to chi_tiet_me_san_xuat
            DB::statement('
                INSERT INTO chi_tiet_me_san_xuat 
                    (me_san_xuat_id, cong_thuc_id, san_pham_id, so_luong_du_kien, so_luong_that_bai, so_luong_thuc_te, ti_le_hong, created_at, updated_at)
                SELECT 
                    ms.id,
                    ms.cong_thuc_id,
                    ct.san_pham_id,
                    ms.so_luong_du_kien,
                    ms.so_luong_that_bai,
                    ms.so_luong_thuc_te,
                    ms.ti_le_hong,
                    ms.created_at,
                    ms.updated_at
                FROM me_san_xuat ms
                INNER JOIN cong_thuc_san_xuat ct ON ms.cong_thuc_id = ct.id
                WHERE ms.cong_thuc_id IS NOT NULL
            ');
        }
        
        // Drop the old columns from me_san_xuat
        Schema::table('me_san_xuat', function (Blueprint $table) {
            if (Schema::hasColumn('me_san_xuat', 'cong_thuc_id')) {
                $table->dropForeign(['cong_thuc_id']);
                $table->dropColumn('cong_thuc_id');
            }
            if (Schema::hasColumn('me_san_xuat', 'so_luong_du_kien')) {
                $table->dropColumn('so_luong_du_kien');
            }
            if (Schema::hasColumn('me_san_xuat', 'so_luong_that_bai')) {
                $table->dropColumn('so_luong_that_bai');
            }
            if (Schema::hasColumn('me_san_xuat', 'so_luong_thuc_te')) {
                $table->dropColumn('so_luong_thuc_te');
            }
            if (Schema::hasColumn('me_san_xuat', 'ti_le_hong')) {
                $table->dropColumn('ti_le_hong');
            }
        });
    }

    public function down(): void
    {
        // Add back the columns
        Schema::table('me_san_xuat', function (Blueprint $table) {
            $table->foreignId('cong_thuc_id')->nullable()->after('ma_me')->constrained('cong_thuc_san_xuat');
            $table->integer('so_luong_du_kien')->default(0)->after('buoi');
            $table->integer('so_luong_that_bai')->default(0)->after('so_luong_du_kien');
            $table->integer('so_luong_thuc_te')->default(0)->after('so_luong_that_bai');
            $table->decimal('ti_le_hong', 5, 2)->default(0)->after('so_luong_thuc_te');
        });
        
        // Migrate data back (take first detail only)
        DB::statement('
            UPDATE me_san_xuat ms
            INNER JOIN (
                SELECT me_san_xuat_id, MIN(id) as first_detail_id
                FROM chi_tiet_me_san_xuat
                GROUP BY me_san_xuat_id
            ) fd ON ms.id = fd.me_san_xuat_id
            INNER JOIN chi_tiet_me_san_xuat ct ON ct.id = fd.first_detail_id
            SET 
                ms.cong_thuc_id = ct.cong_thuc_id,
                ms.so_luong_du_kien = ct.so_luong_du_kien,
                ms.so_luong_that_bai = ct.so_luong_that_bai,
                ms.so_luong_thuc_te = ct.so_luong_thuc_te,
                ms.ti_le_hong = ct.ti_le_hong
        ');
    }
};
