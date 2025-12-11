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
        Schema::table('ca_lam_viec', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_template_id')->nullable()->after('diem_ban_id');
            
            // FK to shift_templates (optional - for quick create from template)
            $table->foreign('shift_template_id')
                  ->references('id')
                  ->on('shift_templates')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ca_lam_viec', function (Blueprint $table) {
            $table->dropForeign(['shift_template_id']);
            $table->dropColumn('shift_template_id');
        });
    }
};
