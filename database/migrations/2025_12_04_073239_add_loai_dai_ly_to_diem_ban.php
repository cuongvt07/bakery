<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diem_ban', function (Blueprint $table) {
            $table->enum('loai_dai_ly', ['via_he', 'rieng_tu'])
                  ->default('via_he')
                  ->after('trang_thai')
                  ->comment('Loại đại lý: vỉa hè (không tủ lạnh) hoặc riêng tư (có tủ lạnh)');
        });
    }

    public function down(): void
    {
        Schema::table('diem_ban', function (Blueprint $table) {
            $table->dropColumn('loai_dai_ly');
        });
    }
};
