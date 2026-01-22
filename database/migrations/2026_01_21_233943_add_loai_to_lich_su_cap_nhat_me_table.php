<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_su_cap_nhat_me', function (Blueprint $table) {
            $table->enum('loai', ['phan_bo', 'ban', 'hong', 'hoan', 'dieu_chinh'])->default('dieu_chinh')->after('diem_ban_id');
            $table->foreignId('ca_lam_viec_id')->nullable()->after('loai');
        });
    }

    public function down(): void
    {
        Schema::table('lich_su_cap_nhat_me', function (Blueprint $table) {
            $table->dropColumn(['loai', 'ca_lam_viec_id']);
        });
    }
};
